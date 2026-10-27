<?php

namespace App\Services\Kasir;

use App\Models\DaftarPenjualan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiKasirService
{
    /**
     * Menghasilkan nomor nota transaksi unik harian dengan format PJ-YYYYMMDD-XXXX (atau prefix kustom).
     */
    public function generateNomorNota(string $prefix = 'PJ'): string
    {
        $prefixFull = $prefix . '-' . date('Ymd') . '-';

        // Cari transaksi terakhir pada hari yang sama dengan prefix terkait
        $lastNota = DB::table('daftar_penjualan')
            ->where('kode_transaksi', 'like', $prefixFull . '%')
            ->orderByDesc('kode_transaksi')
            ->value('kode_transaksi');

        if ($lastNota) {
            $lastSeq = (int) substr($lastNota, -4);
            $seq = $lastSeq + 1;
        } else {
            $seq = 1;
        }

        return $prefixFull . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Memproses transaksi penjualan kasir secara atomik dengan pessimistic locking.
     *
     * @param array<string, mixed> $dataTransaksi
     * @param int $kasirId
     * @return DaftarPenjualan
     * @throws ValidationException
     */
    public function prosesTransaksi(array $dataTransaksi, int $kasirId): DaftarPenjualan
    {
        return DB::transaction(function () use ($dataTransaksi, $kasirId) {
            $nomorNota = $this->generateNomorNota('PJ');
            $items = $dataTransaksi['items'] ?? [];

            if (empty($items)) {
                throw ValidationException::withMessages([
                    'items' => ['Daftar item belanja tidak boleh kosong.'],
                ]);
            }

            $subtotalSemua = 0.0;
            $detailList = [];

            // 1. Validasi dan kunci baris data barang (pessimistic locking lockForUpdate)
            foreach ($items as $item) {
                $kodeItem = (string) ($item['kode_item'] ?? $item['barang_id'] ?? $item['kode'] ?? '');
                $qty = (int) ($item['qty'] ?? $item['jumlah'] ?? 0);

                if ($qty <= 0) {
                    throw ValidationException::withMessages([
                        'items' => ["Kuantitas untuk item '{$kodeItem}' harus minimal 1."],
                    ]);
                }

                $barang = DB::table('daftar_item')
                    ->where('kode_item', $kodeItem)
                    ->lockForUpdate()
                    ->first();

                if (!$barang) {
                    throw ValidationException::withMessages([
                        'items' => ["Barang dengan kode '{$kodeItem}' tidak ditemukan."],
                    ]);
                }

                // Cek ketersediaan stok riil
                $stokTersedia = (int) $barang->stok;
                if ($stokTersedia < $qty) {
                    throw ValidationException::withMessages([
                        'items' => ["Stok barang '{$barang->nama_item}' tidak mencukupi (sisa: {$stokTersedia}, diminta: {$qty})."],
                    ]);
                }

                $hargaJual = (float) $barang->harga_jual;
                $subtotalItem = $hargaJual * $qty;
                $subtotalSemua += $subtotalItem;

                $detailList[] = [
                    'kode_item' => $kodeItem,
                    'nama_item' => $barang->nama_item,
                    'harga_jual' => $hargaJual,
                    'qty' => $qty,
                    'subtotal' => $subtotalItem,
                    'stok_awal' => $stokTersedia,
                    'batas_minimum' => (int) ($barang->batas_minimum ?? 5),
                    'satuan' => $barang->satuan ?: 'pcs',
                ];
            }

            // 2. Kalkulasi diskon, total akhir, pembayaran, dan kembalian
            $diskon = max(0.0, (float) ($dataTransaksi['diskon'] ?? 0));
            $totalAkhir = max(0.0, $subtotalSemua - $diskon);
            $bayar = (float) ($dataTransaksi['nominal_bayar'] ?? $dataTransaksi['bayar'] ?? 0);

            $metodeBayar = strtoupper((string) ($dataTransaksi['metode_bayar'] ?? 'TUNAI'));
            if (!in_array($metodeBayar, ['TUNAI', 'QRIS', 'TRANSFER'], true)) {
                $metodeBayar = 'TUNAI';
            }

            // Untuk non-tunai jika nominal tidak diisi, otomatis dianggap uang pas
            if ($metodeBayar !== 'TUNAI' && $bayar <= 0) {
                $bayar = $totalAkhir;
            }

            if ($bayar < $totalAkhir) {
                $kekurangan = $totalAkhir - $bayar;
                throw ValidationException::withMessages([
                    'nominal_bayar' => ["Uang pembayaran kurang sebesar Rp " . number_format($kekurangan, 0, ',', '.') . "."],
                    'bayar' => ["Uang pembayaran kurang sebesar Rp " . number_format($kekurangan, 0, ',', '.') . "."],
                ]);
            }

            $kembalian = max(0.0, $bayar - $totalAkhir);
            $namaPelanggan = trim((string) ($dataTransaksi['nama_pelanggan'] ?? '')) ?: 'Umum';

            // 3. Simpan record tabel induk penjualan (daftar_penjualan)
            DB::table('daftar_penjualan')->insert([
                'kode_transaksi' => $nomorNota,
                'nama_pelanggan' => $namaPelanggan,
                'tanggal_transaksi' => now()->toDateString(),
                'total_bayar' => $totalAkhir,
                'id_kasir' => $kasirId > 0 ? $kasirId : null,
                'metode_bayar' => $metodeBayar,
            ]);

            // 4. Kurangi stok dan simpan detail item transaksi (daftar_transaksi)
            foreach ($detailList as $detail) {
                // Pengurangan stok otomatis pada database riil
                DB::table('daftar_item')
                    ->where('kode_item', $detail['kode_item'])
                    ->decrement('stok', $detail['qty']);

                $sisaStok = $detail['stok_awal'] - $detail['qty'];

                // Simpan baris detail transaksi
                DB::table('daftar_transaksi')->insert([
                    'kode_penjualan' => $nomorNota,
                    'kode_item' => $detail['kode_item'],
                    'jumlah_item' => $detail['qty'],
                    'item_keluar' => $detail['qty'],
                    'subtotal' => $detail['subtotal'],
                    'tanggal_log' => now(),
                ]);

                // Hook notifikasi stok menipis jika sisa stok <= batas_minimum
                if ($sisaStok <= $detail['batas_minimum']) {
                    if (class_exists(\App\Services\BatasStokService::class)) {
                        try {
                            app(\App\Services\BatasStokService::class)->kirimPeringatanStokMinimum(
                                $detail['kode_item'],
                                $detail['nama_item'],
                                $sisaStok,
                                $detail['batas_minimum'],
                                $detail['satuan'] ?? 'pcs'
                            );
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::error('Gagal memicu notifikasi stok minimum: ' . $e->getMessage());
                        }
                    }
                }
            }

            // 5. Catat / perbarui saldo_harian jika pembayaran adalah TUNAI
            if ($metodeBayar === 'TUNAI') {
                $today = now()->toDateString();
                $existingSaldo = DB::table('saldo_harian')->where('tanggal', $today)->first();

                if ($existingSaldo) {
                    DB::table('saldo_harian')
                        ->where('tanggal', $today)
                        ->increment('saldo_akhir', $totalAkhir);
                } else {
                    DB::table('saldo_harian')->insert([
                        'tanggal' => $today,
                        'saldo_awal' => 0.00,
                        'saldo_akhir' => $totalAkhir,
                        'diatur_oleh' => $kasirId > 0 ? $kasirId : null,
                        'dibuat_pada' => now(),
                    ]);
                }
            }

            // 6. Catat ke log aktivitas (audit trail immutable)
            DB::table('log_aktivitas')->insert([
                'waktu' => now(),
                'keterangan' => "Transaksi penjualan kasir {$nomorNota} berhasil diproses ({$metodeBayar}). Total: Rp " . number_format($totalAkhir, 0, ',', '.'),
                'tipe_aksi' => 'PENJUALAN',
                'id_pengguna' => $kasirId > 0 ? $kasirId : null,
            ]);

            // 7. Muat dan pasang atribut transaksi yang berhasil
            /** @var DaftarPenjualan $transaksi */
            $transaksi = DaftarPenjualan::with(['transaksi.item'])
                ->where('kode_transaksi', $nomorNota)
                ->first();

            $transaksi->nomor_nota = $nomorNota;
            $transaksi->subtotal = $subtotalSemua;
            $transaksi->diskon = $diskon;
            $transaksi->total_akhir = $totalAkhir;
            $transaksi->nominal_bayar = $bayar;
            $transaksi->uang_dibayar = $bayar;
            $transaksi->kembalian = $kembalian;

            return $transaksi;
        });
    }

    /**
     * Mengambil data transaksi lengkap beserta relasi detail barang dan kasir.
     */
    public function getDetailTransaksi(string $nomorNota): ?DaftarPenjualan
    {
        /** @var ?DaftarPenjualan $transaksi */
        $transaksi = DaftarPenjualan::with(['transaksi.item'])
            ->where('kode_transaksi', $nomorNota)
            ->first();

        if ($transaksi) {
            $kasir = null;
            if ($transaksi->id_kasir) {
                $kasir = DB::table('pengguna')
                    ->where('id_pengguna', $transaksi->id_kasir)
                    ->select('id_pengguna', 'nama', 'username', 'role')
                    ->first();
            }
            $transaksi->kasir = $kasir;
        }

        return $transaksi;
    }
}

