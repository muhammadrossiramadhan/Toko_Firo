<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LaporanPenjualanService
{
    /**
     * Menghitung ringkasan data penjualan untuk kartu statistik.
     */
    public function ringkasan(string $mulai, string $sampai): array
    {
        // Total penjualan dari daftar_penjualan (rentang tanggal inklusif)
        $penjualanQuery = DB::table('daftar_penjualan')
            ->whereDate('tanggal_transaksi', '>=', $mulai)
            ->whereDate('tanggal_transaksi', '<=', $sampai);

        $totalPenjualan = (float) $penjualanQuery->sum('total_bayar');
        $jumlahNota = (int) $penjualanQuery->count();

        // Jika total_bayar 0, periksa apakah ada subtotal pada daftar_transaksi
        if ($totalPenjualan == 0) {
            $subtotalTrx = (float) DB::table('daftar_transaksi as dt')
                ->join('daftar_penjualan as dp', 'dt.kode_penjualan', '=', 'dp.kode_transaksi')
                ->whereDate('dp.tanggal_transaksi', '>=', $mulai)
                ->whereDate('dp.tanggal_transaksi', '<=', $sampai)
                ->sum('dt.subtotal');
            if ($subtotalTrx != 0) {
                $totalPenjualan = $subtotalTrx;
            }
        }

        // Akumulasi barang terjual bersih (item_keluar - retur_penjualan)
        $barangTerjual = (int) DB::table('daftar_transaksi as dt')
            ->join('daftar_penjualan as dp', 'dt.kode_penjualan', '=', 'dp.kode_transaksi')
            ->whereDate('dp.tanggal_transaksi', '>=', $mulai)
            ->whereDate('dp.tanggal_transaksi', '<=', $sampai)
            ->sum(DB::raw('COALESCE(dt.item_keluar, 0) - COALESCE(dt.retur_penjualan, 0)'));

        if ($barangTerjual < 0) {
            $barangTerjual = 0;
        }

        // Barang terlaris dalam periode
        $terlaris = DB::table('daftar_transaksi as dt')
            ->join('daftar_penjualan as dp', 'dt.kode_penjualan', '=', 'dp.kode_transaksi')
            ->join('daftar_item as di', 'dt.kode_item', '=', 'di.kode_item')
            ->whereDate('dp.tanggal_transaksi', '>=', $mulai)
            ->whereDate('dp.tanggal_transaksi', '<=', $sampai)
            ->select('di.nama_item', DB::raw('SUM(COALESCE(dt.item_keluar, 0) - COALESCE(dt.retur_penjualan, 0)) as total_terjual'))
            ->groupBy('di.kode_item', 'di.nama_item')
            ->having('total_terjual', '>', 0)
            ->orderByDesc('total_terjual')
            ->first();

        $barangTerlaris = $terlaris ? $terlaris->nama_item : null;

        // Estimasi laba bersih = total penjualan dikurangi (harga_pokok saat ini di daftar_item * Qty bersih)
        $totalHpp = (float) DB::table('daftar_transaksi as dt')
            ->join('daftar_penjualan as dp', 'dt.kode_penjualan', '=', 'dp.kode_transaksi')
            ->join('daftar_item as di', 'dt.kode_item', '=', 'di.kode_item')
            ->whereDate('dp.tanggal_transaksi', '>=', $mulai)
            ->whereDate('dp.tanggal_transaksi', '<=', $sampai)
            ->sum(DB::raw('(COALESCE(dt.item_keluar, 0) - COALESCE(dt.retur_penjualan, 0)) * di.harga_pokok'));

        $labaBersih = $totalPenjualan - $totalHpp;

        return [
            'total_penjualan' => $totalPenjualan,
            'jumlah_nota' => $jumlahNota,
            'barang_terjual' => $barangTerjual,
            'barang_terlaris' => $barangTerlaris,
            'pembanding_penjualan' => 'Belum ada pembanding',
            'pembanding_nota' => 'Belum ada pembanding',
            'laba_bersih' => $labaBersih,
        ];
    }

    /**
     * Mengambil riwayat transaksi dengan paginasi dan filter tanggal.
     */
    public function transaksi(string $mulai, string $sampai, int $perHalaman = 5): LengthAwarePaginator
    {
        return DB::table('daftar_penjualan as dp')
            ->leftJoin('pengguna as p', 'dp.id_kasir', '=', 'p.id_pengguna')
            ->leftJoin('daftar_transaksi as dt', 'dp.kode_transaksi', '=', 'dt.kode_penjualan')
            ->whereDate('dp.tanggal_transaksi', '>=', $mulai)
            ->whereDate('dp.tanggal_transaksi', '<=', $sampai)
            ->select([
                'dp.tanggal_transaksi as tanggal',
                'dp.kode_transaksi',
                DB::raw("COALESCE(p.nama, '-') as nama_kasir"),
                DB::raw("COUNT(DISTINCT dt.kode_item) as jumlah_item"),
                DB::raw("CAST(COALESCE(SUM(CASE WHEN dt.retur_penjualan > 0 THEN dt.retur_penjualan ELSE dt.item_keluar END), 0) AS UNSIGNED) as total_qty"),
                DB::raw("COALESCE(CASE WHEN SUM(dt.subtotal) != 0 THEN SUM(dt.subtotal) ELSE dp.total_bayar END, 0) as subtotal"),
            ])
            ->groupBy('dp.kode_transaksi', 'dp.tanggal_transaksi', 'dp.id_kasir', 'p.id_pengguna', 'p.nama', 'dp.total_bayar')
            ->orderByDesc('dp.tanggal_transaksi')
            ->orderByDesc('dp.kode_transaksi')
            ->paginate($perHalaman)
            ->withQueryString();
    }
}

