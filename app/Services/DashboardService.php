<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Mengambil 4 kartu metrik ringkasan hari ini.
     */
    public function ringkasan(): array
    {
        $hariIni = date('Y-m-d');
        $kemarin = date('Y-m-d', strtotime('-1 day'));

        // 1. Penjualan Hari Ini
        $penjualanHariIni = (float) DB::table('daftar_penjualan')
            ->whereDate('tanggal_transaksi', $hariIni)
            ->sum('total_bayar');

        if ($penjualanHariIni == 0) {
            $subtotalTrx = (float) DB::table('daftar_transaksi as dt')
                ->join('daftar_penjualan as dp', 'dt.kode_penjualan', '=', 'dp.kode_transaksi')
                ->whereDate('dp.tanggal_transaksi', $hariIni)
                ->sum('dt.subtotal');
            if ($subtotalTrx != 0) {
                $penjualanHariIni = $subtotalTrx;
            }
        }

        $penjualanKemarin = (float) DB::table('daftar_penjualan')
            ->whereDate('tanggal_transaksi', $kemarin)
            ->sum('total_bayar');

        $badgePenjualan = '+0%';
        if ($penjualanKemarin > 0) {
            $diffPersen = round((($penjualanHariIni - $penjualanKemarin) / $penjualanKemarin) * 100);
            $badgePenjualan = ($diffPersen >= 0 ? '+' : '') . $diffPersen . '%';
        } elseif ($penjualanHariIni > 0) {
            $badgePenjualan = '+100%';
        }

        // 2. Jumlah Transaksi
        $jumlahTransaksi = (int) DB::table('daftar_penjualan')
            ->whereDate('tanggal_transaksi', $hariIni)
            ->count();

        $transaksiKemarin = (int) DB::table('daftar_penjualan')
            ->whereDate('tanggal_transaksi', $kemarin)
            ->count();

        $selisihTrx = $jumlahTransaksi - $transaksiKemarin;
        $badgeTransaksi = ($selisihTrx >= 0 ? '+' : '') . $selisihTrx;

        // 3. Stok Menipis (stok <= batas_minimum, non-jasa)
        $stokMenipisCount = (int) DB::table('daftar_item')
            ->where(function ($q) {
                $q->where('tipe_item', '!=', 'JASA')
                    ->orWhereNull('tipe_item');
            })
            ->whereRaw('stok <= batas_minimum')
            ->count();

        $stokHabisCount = (int) DB::table('daftar_item')
            ->where(function ($q) {
                $q->where('tipe_item', '!=', 'JASA')
                    ->orWhereNull('tipe_item');
            })
            ->where('stok', '<=', 0)
            ->count();

        // 4. Laba Hari Ini (Penjualan hari ini - HPP hari ini)
        $totalHppHariIni = (float) DB::table('daftar_transaksi as dt')
            ->join('daftar_penjualan as dp', 'dt.kode_penjualan', '=', 'dp.kode_transaksi')
            ->join('daftar_item as di', 'dt.kode_item', '=', 'di.kode_item')
            ->whereDate('dp.tanggal_transaksi', $hariIni)
            ->sum(DB::raw('(COALESCE(dt.item_keluar, 0) - COALESCE(dt.retur_penjualan, 0)) * di.harga_pokok'));

        $labaHariIni = $penjualanHariIni - $totalHppHariIni;
        $badgeLaba = '+0%';
        if ($penjualanHariIni > 0) {
            $marginPersen = round(($labaHariIni / $penjualanHariIni) * 100);
            $badgeLaba = ($marginPersen >= 0 ? '+' : '') . $marginPersen . '%';
        }

        return [
            'penjualan_hari_ini' => [
                'label' => 'Penjualan Hari Ini',
                'nilai' => 'Rp ' . number_format($penjualanHariIni, 0, ',', '.'),
                'badge' => $badgePenjualan,
                'tipe' => 'positif',
            ],
            'jumlah_transaksi' => [
                'label' => 'Jumlah Transaksi',
                'nilai' => (string) $jumlahTransaksi,
                'badge' => $badgeTransaksi,
                'tipe' => 'positif',
            ],
            'stok_menipis' => [
                'label' => 'Stok Menipis',
                'nilai' => (string) $stokMenipisCount,
                'subteks' => $stokHabisCount . ' habis',
                'tipe' => 'peringatan',
            ],
            'laba_hari_ini' => [
                'label' => 'Laba Hari Ini',
                'nilai' => 'Rp ' . number_format($labaHariIni, 0, ',', '.'),
                'badge' => $badgeLaba,
                'tipe' => 'positif',
            ],
        ];
    }

    /**
     * Mengambil volume penjualan 7 hari terakhir (termasuk hari ini, urut lama ke baru).
     * Hari tanpa transaksi bernilai 0.
     */
    public function penjualanMingguan(): array
    {
        $namaHariMap = [
            'Sun' => 'Min',
            'Mon' => 'Sen',
            'Tue' => 'Sel',
            'Wed' => 'Rab',
            'Thu' => 'Kam',
            'Fri' => 'Jum',
            'Sat' => 'Sab',
        ];

        $tanggalMulai = date('Y-m-d', strtotime('-6 days'));
        $tanggalSampai = date('Y-m-d');

        // Query penjualan terkelompok per tanggal
        $penjualanPerHari = DB::table('daftar_penjualan')
            ->whereDate('tanggal_transaksi', '>=', $tanggalMulai)
            ->whereDate('tanggal_transaksi', '<=', $tanggalSampai)
            ->select('tanggal_transaksi', DB::raw('SUM(total_bayar) as total'))
            ->groupBy('tanggal_transaksi')
            ->pluck('total', 'tanggal_transaksi')
            ->toArray();

        // Bangun data 7 hari berurutan dari lama ke baru
        $hariList = [];
        $maxNominal = 0;

        for ($i = 6; $i >= 0; $i--) {
            $tgl = date('Y-m-d', strtotime("-$i days"));
            $nominal = isset($penjualanPerHari[$tgl]) ? (float) $penjualanPerHari[$tgl] : 0.0;

            if ($nominal == 0) {
                // Cek subtotal jika total_bayar 0
                $subtotal = (float) DB::table('daftar_transaksi as dt')
                    ->join('daftar_penjualan as dp', 'dt.kode_penjualan', '=', 'dp.kode_transaksi')
                    ->whereDate('dp.tanggal_transaksi', $tgl)
                    ->sum('dt.subtotal');
                if ($subtotal != 0) {
                    $nominal = $subtotal;
                }
            }

            if ($nominal > $maxNominal) {
                $maxNominal = $nominal;
            }

            $singkatanHari = $namaHariMap[date('D', strtotime($tgl))] ?? date('D', strtotime($tgl));

            $hariList[] = [
                'tanggal' => $tgl,
                'hari' => $singkatanHari,
                'nominal_raw' => $nominal,
            ];
        }

        // Hitung persentase bar dan tandai puncak
        $hasil = [];
        foreach ($hariList as $item) {
            $persen = $maxNominal > 0 ? (int) round(($item['nominal_raw'] / $maxNominal) * 100) : 0;
            $isPuncak = ($maxNominal > 0 && $item['nominal_raw'] === $maxNominal);

            $hasil[] = [
                'hari' => $item['hari'],
                'persen' => $persen,
                'is_puncak' => $isPuncak,
                'nominal' => 'Rp ' . number_format($item['nominal_raw'], 0, ',', '.'),
            ];
        }

        return $hasil;
    }

    /**
     * Mengambil daftar produk terlaris hari ini.
     */
    public function topBarang(int $limit = 5): array
    {
        $hariIni = date('Y-m-d');

        $rows = DB::table('daftar_transaksi as dt')
            ->join('daftar_penjualan as dp', 'dt.kode_penjualan', '=', 'dp.kode_transaksi')
            ->join('daftar_item as di', 'dt.kode_item', '=', 'di.kode_item')
            ->whereDate('dp.tanggal_transaksi', $hariIni)
            ->select([
                'di.nama_item',
                'di.jenis',
                'di.tipe_item',
                DB::raw('SUM(COALESCE(dt.item_keluar, 0) - COALESCE(dt.retur_penjualan, 0)) as total_terjual'),
            ])
            ->groupBy('di.kode_item', 'di.nama_item', 'di.jenis', 'di.tipe_item')
            ->having('total_terjual', '>', 0)
            ->orderByDesc('total_terjual')
            ->limit($limit)
            ->get();

        $result = [];
        $nomor = 1;

        foreach ($rows as $row) {
            $unit = ($row->tipe_item === 'JASA') ? 'layanan' : 'terjual';
            $result[] = (object) [
                'nomor' => $nomor++,
                'nama' => $row->nama_item,
                'terjual' => $row->total_terjual . ' ' . $unit,
                'kategori' => $row->jenis ?: '-',
            ];
        }

        return $result;
    }

    /**
     * Mengambil 10 baris aktivitas terbaru dari log_aktivitas.
     */
    public function aktivitasTerbaru(int $limit = 10): array
    {
        $logs = DB::table('log_aktivitas as la')
            ->leftJoin('pengguna as p', 'la.id_pengguna', '=', 'p.id_pengguna')
            ->select([
                'la.waktu',
                'la.keterangan',
                'la.tipe_aksi',
                'p.nama as nama_pengguna',
            ])
            ->orderByDesc('la.waktu')
            ->orderByDesc('la.id_log')
            ->limit($limit)
            ->get();

        $result = [];
        foreach ($logs as $log) {
            $waktuFormatted = date('H:i', strtotime($log->waktu));

            $tipeAksi = (string) $log->tipe_aksi;
            $keterangan = (string) $log->keterangan;

            $tipe = 'transaksi';
            if ($tipeAksi === 'BATAS_STOK' || str_contains(strtolower($keterangan), 'habis') || str_contains(strtolower($tipeAksi), 'peringatan')) {
                $tipe = 'peringatan';
            } elseif (str_contains(strtoupper($tipeAksi), 'LOGIN') || str_contains(strtoupper($tipeAksi), 'OTP')) {
                $tipe = 'autentikasi';
            } elseif (str_contains(strtoupper($tipeAksi), 'ITEM') || str_contains(strtoupper($tipeAksi), 'HARGA')) {
                $tipe = 'master_data';
            }

            // Jika ada nama pengguna null, tampilkan keterangan dengan format aman
            $pelaku = $log->nama_pengguna ?: 'Sistem';

            $result[] = (object) [
                'waktu' => $waktuFormatted,
                'keterangan' => $keterangan,
                'tipe' => $tipe,
                'pelaku' => $pelaku,
            ];
        }

        return $result;
    }
}

