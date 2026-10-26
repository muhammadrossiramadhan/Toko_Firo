<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PembelianService
{
    /**
     * Mengambil daftar barang yang stoknya <= batas_minimum (non-jasa, aturan K11).
     *
     * @return Collection
     */
    public function getBarangPerluRestock(): Collection
    {
        $items = DB::table('daftar_item')
            ->where(function ($q) {
                $q->where('tipe_item', '!=', 'JASA')
                    ->orWhereNull('tipe_item');
            })
            ->whereRaw('stok <= batas_minimum')
            ->orderBy('stok', 'asc')
            ->orderBy('nama_item', 'asc')
            ->get();

        return $items->map(function ($item) {
            $status = ((int) $item->stok <= 0) ? 'Habis' : 'Menipis';

            return (object) [
                'kode_item' => $item->kode_item,
                'nama' => $item->nama_item,
                'stok' => (int) $item->stok,
                'satuan' => $item->satuan ?: 'pcs',
                'batas_minimum' => (int) $item->batas_minimum,
                'status' => $status,
            ];
        });
    }

    /**
     * Mengambil daftar riwayat transaksi pembelian beserta nama barang dan jumlahnya.
     *
     * @param int $perHalaman
     * @return Collection
     */
    public function getRiwayatPembelian(int $perHalaman = 10): Collection
    {
        $rows = DB::table('daftar_pembelian as dp')
            ->join('daftar_transaksi as dt', 'dp.kode_transaksi', '=', 'dt.kode_pembelian')
            ->join('daftar_item as di', 'dt.kode_item', '=', 'di.kode_item')
            ->select([
                'dp.kode_transaksi as kode',
                'dp.tanggal_transaksi',
                'dt.tanggal_log',
                'di.nama_item as barang',
                'dt.item_masuk',
                'di.satuan',
                'dt.subtotal',
                'dp.total_bayar',
                'di.harga_pokok',
            ])
            ->orderByDesc('dp.tanggal_transaksi')
            ->orderByDesc('dt.id_detail')
            ->limit($perHalaman)
            ->get();

        return $rows->map(function ($row) {
            $waktu = $row->tanggal_log
                ? date('Y-m-d H:i', strtotime($row->tanggal_log))
                : date('Y-m-d 00:00', strtotime($row->tanggal_transaksi));

            $total = (float) $row->subtotal;
            if ($total <= 0) {
                $total = (float) $row->total_bayar;
            }
            if ($total <= 0) {
                $total = (float) $row->item_masuk * (float) $row->harga_pokok;
            }

            return (object) [
                'waktu' => $waktu,
                'kode' => $row->kode,
                'barang' => $row->barang,
                'jumlah' => (int) $row->item_masuk . ' ' . ($row->satuan ?: 'pcs'),
                'total' => $total,
                'status' => 'Selesai',
            ];
        });
    }

    /**
     * Mencatat transaksi pembelian restock barang ke database menggunakan Stored Procedure.
     *
     * @param string $kodeTransaksi
     * @param string $namaSupplier
     * @param int|null $idPengguna
     * @param array $items Array berisikan ['kode_item' => ..., 'jumlah' => ...]
     * @return void
     */
    public function catatPembelian(string $kodeTransaksi, string $namaSupplier, ?int $idPengguna, array $items): void
    {
        if (trim($namaSupplier) === '') {
            throw new InvalidArgumentException('Nama supplier wajib diisi.');
        }

        if (empty($items)) {
            throw new InvalidArgumentException('Daftar item pembelian tidak boleh kosong.');
        }

        DB::transaction(function () use ($kodeTransaksi, $namaSupplier, $idPengguna, $items) {
            // 1. Panggil Stored Procedure pembuatan header pembelian
            DB::statement('CALL sp_create_pembelian(?, ?, ?)', [
                $kodeTransaksi,
                trim($namaSupplier),
                $idPengguna,
            ]);

            $totalBayar = 0.0;

            // 2. Tambah setiap item via Stored Procedure dan perbarui stok
            foreach ($items as $item) {
                $kodeItem = (string) ($item['kode_item'] ?? '');
                $qty = (int) ($item['jumlah'] ?? $item['qty'] ?? 0);

                if ($qty <= 0) {
                    throw new InvalidArgumentException('Jumlah item pembelian harus lebih dari 0.');
                }

                // Panggil Stored Procedure sp_tambah_item_pembelian
                DB::statement('CALL sp_tambah_item_pembelian(?, ?, ?)', [
                    $kodeTransaksi,
                    $kodeItem,
                    $qty,
                ]);

                // Naikkan stok barang pada master daftar_item
                DB::table('daftar_item')
                    ->where('kode_item', $kodeItem)
                    ->increment('stok', $qty);

                // Hitung subtotal dan perbarui pada baris transaksi
                $hargaPokok = (float) DB::table('daftar_item')->where('kode_item', $kodeItem)->value('harga_pokok');
                $subtotal = $qty * $hargaPokok;
                $totalBayar += $subtotal;

                DB::table('daftar_transaksi')
                    ->where('kode_pembelian', $kodeTransaksi)
                    ->where('kode_item', $kodeItem)
                    ->update(['subtotal' => $subtotal]);
            }

            // Perbarui total_bayar pada daftar_pembelian
            DB::table('daftar_pembelian')
                ->where('kode_transaksi', $kodeTransaksi)
                ->update(['total_bayar' => $totalBayar]);

            // Catat ke log_aktivitas untuk audit trail
            DB::table('log_aktivitas')->insert([
                'waktu' => now(),
                'keterangan' => "Pembelian restock: {$kodeTransaksi} dari {$namaSupplier}",
                'tipe_aksi' => 'PEMBELIAN',
                'id_pengguna' => $idPengguna,
            ]);
        });
    }
}

