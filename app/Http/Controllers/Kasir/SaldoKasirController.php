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
        $tanggal = $hariIni->toDateString();
        $kasirId = auth()->id();

        // 1. Ambil data saldo awal hari ini dari saldo_harian (read-only, tanpa mutasi updateOrInsert)
        $saldoHariIni = DB::table('saldo_harian')
            ->where('tanggal', $tanggal)
            ->first();

        if ($saldoHariIni && $saldoHariIni->saldo_awal !== null) {
            $saldoAwal = (float) $saldoHariIni->saldo_awal;
        } else {
            // Fallback ke saldo_akhir hari sebelumnya
            $saldoSebelumnya = DB::table('saldo_harian')
                ->where('tanggal', '<', $tanggal)
                ->orderByDesc('tanggal')
                ->value('saldo_akhir');
            $saldoAwal = (float) ($saldoSebelumnya ?? 0.0);
        }

        // 2. Ambil transaksi penjualan hari ini untuk kasir bersangkutan
        $penjualanQuery = DB::table('daftar_penjualan')
            ->whereDate('tanggal_transaksi', $tanggal)
            ->when($kasirId, fn ($q) => $q->where('id_kasir', $kasirId));

        $penjualan = (clone $penjualanQuery)
            ->orderByDesc('kode_transaksi')
            ->get();

        // 3. Akumulasi transaksi TUNAI dan NON-TUNAI
        $totalTunai = (float) (clone $penjualanQuery)
            ->whereRaw('UPPER(COALESCE(metode_bayar, "TUNAI")) = ?', ['TUNAI'])
            ->sum('total_bayar');

        $totalNonTunai = (float) (clone $penjualanQuery)
            ->whereRaw('UPPER(COALESCE(metode_bayar, "TUNAI")) != ?', ['TUNAI'])
            ->sum('total_bayar');

        $totalSemua = (float) $penjualan->sum('total_bayar');
        $jumlahTransaksi = $penjualan->count();

        // 4. Hitung kas keluar / retur penjualan dari transaksi hari ini
        $kodeTransaksi = $penjualan->pluck('kode_transaksi');
        $kasKeluar = 0.0;
        if ($kodeTransaksi->isNotEmpty()) {
            $kasKeluar = (float) (DB::table('daftar_transaksi')
                ->join('daftar_item', 'daftar_transaksi.kode_item', '=', 'daftar_item.kode_item')
                ->whereIn('daftar_transaksi.kode_penjualan', $kodeTransaksi)
                ->select(DB::raw('SUM(COALESCE(daftar_transaksi.retur_penjualan, 0) * COALESCE(daftar_item.harga_jual, 0)) as total_retur'))
                ->value('total_retur') ?? 0.0);
        }

        // 5. Hitung saldo akhir kas fisik kasir secara dinamis (Saldo Awal + Penjualan Tunai - Kas Keluar)
        $saldoAkhir = $saldoAwal + $totalTunai - $kasKeluar;

        $ringkasan = [
            'tanggal'               => $hariIni->translatedFormat('l, d M Y'),
            'nominal_saldo'         => 'Rp ' . number_format($saldoAkhir, 0, ',', '.'),
            'saldo_awal'            => 'Rp ' . number_format($saldoAwal, 0, ',', '.'),
            'modal_awal'            => 'Rp ' . number_format($saldoAwal, 0, ',', '.'),
            'total_penjualan_tunai' => 'Rp ' . number_format($totalTunai, 0, ',', '.'),
            'total_non_tunai'       => 'Rp ' . number_format($totalNonTunai, 0, ',', '.'),
            'kas_keluar'            => 'Rp ' . number_format($kasKeluar, 0, ',', '.'),
            'saldo_akhir'           => 'Rp ' . number_format($saldoAkhir, 0, ',', '.'),
            'total_transaksi'       => $jumlahTransaksi,
            'rata_rata'             => 'Rp ' . number_format(
                $jumlahTransaksi > 0 ? $totalSemua / $jumlahTransaksi : 0, 0, ',', '.'
            ),
        ];

        // 6. Ambil detail barang untuk setiap transaksi
        $detail = DB::table('daftar_transaksi')
            ->whereIn('kode_penjualan', $kodeTransaksi)
            ->select(
                'kode_penjualan',
                DB::raw('GROUP_CONCAT(DISTINCT kode_item SEPARATOR ", ") as item'),
                DB::raw('MAX(tanggal_log) as waktu')
            )
            ->groupBy('kode_penjualan')
            ->get()
            ->keyBy('kode_penjualan');

        // Susun riwayat transaksi dengan metode pembayaran yang akurat
        $riwayatTransaksi = $penjualan->map(function ($trx) use ($detail) {
            $d = $detail->get($trx->kode_transaksi);
            $metodeRaw = strtoupper((string) ($trx->metode_bayar ?? 'TUNAI'));
            $metodeLabel = ($metodeRaw === 'TUNAI') ? 'Tunai' : ($trx->metode_bayar ?: 'QRIS');

            return [
                'waktu' => $d && $d->waktu
                    ? Carbon::parse($d->waktu)->format('H:i')
                    : (isset($trx->tanggal_transaksi) ? Carbon::parse($trx->tanggal_transaksi)->format('H:i') : '--:--'),
                'no_transaksi' => $trx->kode_transaksi,
                'item' => $d?->item ?? 'Tidak ada detail barang',
                'total' => 'Rp ' . number_format((float) $trx->total_bayar, 0, ',', '.'),
                'metode' => $metodeLabel,
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