<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SaldoKasirController extends Controller
{
    public function index(): View
    {
        $hariIni = Carbon::today();

        // Ambil penjualan hari ini.
        $penjualan = DB::table('daftar_penjualan')
            ->whereDate('tanggal_transaksi', $hariIni)
            ->orderByDesc('kode_transaksi')
            ->get();

        // Hitung ringkasan penjualan.
        $total = $penjualan->sum('total_bayar');
        $jumlah = $penjualan->count();

        $hariIni = Carbon::today();
        $total = (float) $penjualan->sum('total_bayar');

        // Cari saldo akhir dari hari sebelumnya
        $saldoHarianSebelumnya = DB::table('saldo_harian')
            ->whereDate('tanggal', '<', $hariIni->toDateString())
            ->orderByDesc('tanggal')
            ->value('saldo_akhir');

        // Ambil data saldo today
        $saldohariini = DB::table('saldo_harian')
            ->where('tanggal', $hariIni->toDateString())
            ->first();

        // Saldo awal hari ini
        $saldoawal = $saldohariini
            ? (float) $saldohariini->saldo_awal
            : (float) ($saldoHarianSebelumnya ?? 0); 

        // Hitung saldo akhir hari ini
        $saldoakhir = $saldoawal + $total;

        // Simpan saldo hari ini ke DB
        DB::table('saldo_harian')->updateOrInsert(
            ['tanggal' => $hariIni->toDateString()],
            [
                'saldo_awal' => $saldoawal,
                // 'total_transaksi' => $total,
                'diatur_oleh' => $saldohariini->diatur_oleh ?? auth()->id(),
                'saldo_akhir' => $saldoakhir,
                'dibuat_pada' => $saldohariini->dibuat_pada ?? now(),
            ]
        );

        $saldoHarian = DB::table('saldo_harian')
            ->whereDate('tanggal', $hariIni)
            ->first();

        $modalAwal = $saldoHarian->saldo_awal ?? 0;

        $ringkasan = [
             'tanggal' => $hariIni->translatedFormat('l, d M Y'),
            'nominal_saldo' => 'Rp ' . number_format(
            $modalAwal + $total, 0, ',', '.'
            ),
            'total_transaksi' => $jumlah,
            'rata_rata' => 'Rp ' . number_format(
            $jumlah > 0 ? $total / $jumlah : 0, 0, ',', '.'
            ),
            'modal_awal' => 'Rp ' . number_format(
            $modalAwal, 0, ',', '.'
    ),
];

        // Ambil kode barang untuk setiap transaksi.
        $kodeTransaksi = $penjualan->pluck('kode_transaksi');

        $detail = DB::table('daftar_transaksi')
            ->whereIn('kode_penjualan', $kodeTransaksi)
            ->select(
                'kode_penjualan',
                DB::raw(
                    'GROUP_CONCAT(DISTINCT kode_item SEPARATOR ", ") as item'
                ),
                DB::raw('MAX(tanggal_log) as waktu')
            )
            ->groupBy('kode_penjualan')
            ->get()
            ->keyBy('kode_penjualan');

        // Susun data sesuai format yang digunakan view.
        $riwayatTransaksi = $penjualan->map(function ($trx) use ($detail) {
            $d = $detail->get($trx->kode_transaksi);

            return [
                'waktu' => $d && $d->waktu
                    ? Carbon::parse($d->waktu)->format('H:i')
                    : '--:--',
                'no_transaksi' => $trx->kode_transaksi,
                'item' => $d?->item ?? 'Tidak ada detail barang',
                'total' => 'Rp ' . number_format(
                    $trx->total_bayar, 0, ',', '.'
                ),
                'metode' => 'Belum tersedia',
            ];
        });

        $user = auth()->user();
        $nama = $user->name ?? 'Kasir';

        $kasir = [
            'nama' => $nama,
            'inisial' => strtoupper(substr($nama, 0, 1)),
            'role' => 'Kasir',
        ];

        return view('kasir.saldo.index', compact(
            'ringkasan',
            'riwayatTransaksi',
            'kasir'
        ));
    }
}