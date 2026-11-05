<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LaporanPembelianService
{
    /**
     * Menghitung ringkasan data pembelian untuk kartu statistik.
     */
    public function ringkasan(string $mulai, string $sampai): array
    {
        // Total pembelian dari daftar_pembelian (rentang tanggal inklusif)
        $pembelianQuery = DB::table('daftar_pembelian')
            ->whereDate('tanggal_transaksi', '>=', $mulai)
            ->whereDate('tanggal_transaksi', '<=', $sampai);

        $totalPembelian = (float) $pembelianQuery->sum('total_bayar');
        $jumlahNota = (int) $pembelianQuery->count();

        // Jika total_bayar 0, periksa apakah ada subtotal pada daftar_transaksi
        if ($totalPembelian == 0) {
            $subtotalTrx = (float) DB::table('daftar_transaksi as dt')
                ->join('daftar_pembelian as dp', 'dt.kode_pembelian', '=', 'dp.kode_transaksi')
                ->whereDate('dp.tanggal_transaksi', '>=', $mulai)
                ->whereDate('dp.tanggal_transaksi', '<=', $sampai)
                ->sum('dt.subtotal');
            if ($subtotalTrx != 0) {
                $totalPembelian = $subtotalTrx;
            }
        }

        // Akumulasi barang masuk bersih (item_masuk - retur_pembelian)
        $barangMasuk = (int) DB::table('daftar_transaksi as dt')
            ->join('daftar_pembelian as dp', 'dt.kode_pembelian', '=', 'dp.kode_transaksi')
            ->whereDate('dp.tanggal_transaksi', '>=', $mulai)
            ->whereDate('dp.tanggal_transaksi', '<=', $sampai)
            ->sum(DB::raw('COALESCE(dt.item_masuk, 0) - COALESCE(dt.retur_pembelian, 0)'));

        if ($barangMasuk < 0) {
            $barangMasuk = 0;
        }

        return [
            'total_pembelian' => $totalPembelian,
            'jumlah_nota' => $jumlahNota,
            'barang_masuk' => $barangMasuk,
            'pembanding_pembelian' => 'Belum ada pembanding',
            'pembanding_nota' => 'Belum ada pembanding',
            'pembanding_barang' => 'Belum ada pembanding',
        ];
    }

    /**
     * Mengambil riwayat transaksi pembelian dengan paginasi dan filter tanggal.
     */
    public function transaksi(string $mulai, string $sampai, int $perHalaman = 5): LengthAwarePaginator
    {
        return DB::table('daftar_pembelian as dp')
            ->leftJoin('pengguna as p', 'dp.id_pengguna', '=', 'p.id_pengguna')
            ->leftJoin('daftar_transaksi as dt', 'dp.kode_transaksi', '=', 'dt.kode_pembelian')
            ->whereDate('dp.tanggal_transaksi', '>=', $mulai)
            ->whereDate('dp.tanggal_transaksi', '<=', $sampai)
            ->select([
                'dp.tanggal_transaksi as tanggal',
                'dp.kode_transaksi',
                'dp.nama_supplier',
                DB::raw("COALESCE(p.nama, '-') as nama_pengguna"),
                DB::raw("COUNT(DISTINCT dt.kode_item) as jumlah_item"),
                DB::raw("CAST(COALESCE(SUM(dt.item_masuk), 0) AS UNSIGNED) as total_qty"),
                DB::raw("COALESCE(CASE WHEN SUM(dt.subtotal) != 0 THEN SUM(dt.subtotal) ELSE dp.total_bayar END, 0) as total"),
                DB::raw("COALESCE(CASE WHEN SUM(dt.subtotal) != 0 THEN SUM(dt.subtotal) ELSE dp.total_bayar END, 0) as subtotal"),
            ])
            ->groupBy('dp.kode_transaksi', 'dp.tanggal_transaksi', 'dp.nama_supplier', 'dp.id_pengguna', 'p.id_pengguna', 'p.nama', 'dp.total_bayar')
            ->orderByDesc('dp.tanggal_transaksi')
            ->orderByDesc('dp.kode_transaksi')
            ->paginate($perHalaman)
            ->withQueryString();
    }
}

