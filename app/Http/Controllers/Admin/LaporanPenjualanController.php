<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LaporanPenjualanService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPenjualanController extends Controller
{
    /**
     * Jumlah baris riwayat transaksi per halaman paginasi.
     */
    public const PER_HALAMAN = 5;

    public function __construct(
        protected LaporanPenjualanService $laporanService
    ) {}

    /**
     * Menampilkan halaman Laporan Penjualan (Figma P11).
     */
    public function index(Request $request): View
    {
        $pesan = null;
        $rentangInput = (string) $request->query('rentang', 'hari-ini');
        $hariIni = date('Y-m-d');

        // Daftar putih opsi rentang
        $daftarPutihRentang = ['hari-ini', 'hari_ini', '7-hari', 'tujuh_hari', '7_hari', 'bulan-ini', 'bulan_ini', 'kustom'];
        $rentang = in_array($rentangInput, $daftarPutihRentang, true) ? $rentangInput : 'hari-ini';

        if ($rentang === '7-hari' || $rentang === 'tujuh_hari' || $rentang === '7_hari') {
            $rentang = '7-hari';
            $mulai = date('Y-m-d', strtotime('-6 days'));
            $sampai = $hariIni;
        } elseif ($rentang === 'bulan-ini' || $rentang === 'bulan_ini') {
            $rentang = 'bulan-ini';
            $mulai = date('Y-m-01');
            $sampai = $hariIni;
        } elseif ($rentang === 'kustom') {
            $reqMulai = (string) $request->query('mulai');
            $reqSampai = (string) $request->query('sampai');

            $formatValid = $this->validasiTanggal($reqMulai) && $this->validasiTanggal($reqSampai);

            if (! $formatValid || $reqSampai < $reqMulai) {
                $mulai = $hariIni;
                $sampai = $hariIni;
                $pesan = 'Tanggal tidak valid, ditampilkan hari ini.';
            } else {
                $selisihHari = (strtotime($reqSampai) - strtotime($reqMulai)) / 86400;
                if ($selisihHari > 366) {
                    $mulai = $hariIni;
                    $sampai = $hariIni;
                    $pesan = 'Rentang terlalu panjang, maksimal 1 tahun.';
                } else {
                    $mulai = $reqMulai;
                    $sampai = $reqSampai;
                }
            }
        } else {
            $rentang = 'hari-ini';
            $mulai = $hariIni;
            $sampai = $hariIni;
        }

        $filter = [
            'rentang' => $rentang,
            'mulai' => $mulai,
            'sampai' => $sampai,
        ];

        $ringkasan = $this->laporanService->ringkasan($mulai, $sampai);
        $transaksi = $this->laporanService->transaksi($mulai, $sampai, self::PER_HALAMAN);

        return view('admin.laporan.penjualan', [
            'filter' => $filter,
            'ringkasan' => $ringkasan,
            'transaksi' => $transaksi,
            'pesan' => $pesan,
        ]);
    }

    /**
     * Memeriksa apakah format tanggal valid (Y-m-d).
     */
    private function validasiTanggal(?string $tanggal): bool
    {
        if (! $tanggal) {
            return false;
        }

        $d = DateTime::createFromFormat('Y-m-d', $tanggal);

        return $d && $d->format('Y-m-d') === $tanggal;
    }
}
