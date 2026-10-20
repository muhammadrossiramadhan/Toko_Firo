<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class LaporanPenjualanController extends Controller
{
    /**
     * Jumlah baris riwayat transaksi per halaman paginasi.
     */
    public const PER_HALAMAN = 5;

    /**
     * Menampilkan halaman Laporan Penjualan (Figma P11).
     */
    public function index(Request $request): View
    {
        $pesan = null;
        $rentang = (string) $request->query('rentang', 'hari-ini');
        $hariIni = date('Y-m-d');

        if ($rentang === '7-hari') {
            $mulai = date('Y-m-d', strtotime('-6 days'));
            $sampai = $hariIni;
        } elseif ($rentang === 'bulan-ini') {
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
                $mulai = $reqMulai;
                $sampai = $reqSampai;
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

        $ringkasan = [
            'total_penjualan' => 2450000,
            'jumlah_nota' => 12,
            'barang_terjual' => 85,
            'barang_terlaris' => 'Buku Tulis Sinar Dunia 38 Lembar',
            'pembanding_penjualan' => '+12% dari periode sebelumnya',
            'pembanding_nota' => '+2 nota dari periode sebelumnya',
        ];

        $semuaData = $this->dataContoh();
        $page = (int) $request->query('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * self::PER_HALAMAN;
        $items = array_slice($semuaData, $offset, self::PER_HALAMAN);

        $transaksi = new LengthAwarePaginator(
            $items,
            count($semuaData),
            self::PER_HALAMAN,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

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

    /**
     * Data transaksi contoh untuk prototipe tampilan.
     * TODO: ganti dengan query database (Prompt 2)
     */
    private function dataContoh(): array
    {
        return [
            (object) [
                'tanggal' => '2026-10-19',
                'kode_transaksi' => 'TRX-20261019-001',
                'nama_kasir' => 'Siti Aminah',
                'jumlah_item' => 3,
                'total_qty' => 5,
                'subtotal' => 47500,
            ],
            (object) [
                'tanggal' => '2026-10-19',
                'kode_transaksi' => 'TRX-20261019-002',
                'nama_kasir' => 'Budi Santoso',
                'jumlah_item' => 2,
                'total_qty' => 2,
                'subtotal' => 15000,
            ],
            (object) [
                'tanggal' => '2026-10-19',
                'kode_transaksi' => 'TRX-20261019-003',
                'nama_kasir' => 'Siti Aminah',
                'jumlah_item' => 1,
                'total_qty' => 1,
                'subtotal' => -15000,
            ],
            (object) [
                'tanggal' => '2026-10-18',
                'kode_transaksi' => 'TRX-20261018-001',
                'nama_kasir' => 'Budi Santoso',
                'jumlah_item' => 4,
                'total_qty' => 10,
                'subtotal' => 125000,
            ],
            (object) [
                'tanggal' => '2026-10-18',
                'kode_transaksi' => 'TRX-20261018-002',
                'nama_kasir' => 'Siti Aminah',
                'jumlah_item' => 2,
                'total_qty' => 3,
                'subtotal' => 32000,
            ],
            (object) [
                'tanggal' => '2026-10-17',
                'kode_transaksi' => 'TRX-20261017-001',
                'nama_kasir' => 'Budi Santoso',
                'jumlah_item' => 5,
                'total_qty' => 8,
                'subtotal' => 84000,
            ],
            (object) [
                'tanggal' => '2026-10-17',
                'kode_transaksi' => 'TRX-20261017-002',
                'nama_kasir' => 'Siti Aminah',
                'jumlah_item' => 1,
                'total_qty' => 2,
                'subtotal' => 22000,
            ],
            (object) [
                'tanggal' => '2026-10-16',
                'kode_transaksi' => 'TRX-20261016-001',
                'nama_kasir' => 'Budi Santoso',
                'jumlah_item' => 3,
                'total_qty' => 6,
                'subtotal' => 55000,
            ],
            (object) [
                'tanggal' => '2026-10-15',
                'kode_transaksi' => 'TRX-20261015-001',
                'nama_kasir' => 'Siti Aminah',
                'jumlah_item' => 6,
                'total_qty' => 12,
                'subtotal' => 180000,
            ],
            (object) [
                'tanggal' => '2026-10-14',
                'kode_transaksi' => 'TRX-20261014-001',
                'nama_kasir' => 'Budi Santoso',
                'jumlah_item' => 2,
                'total_qty' => 4,
                'subtotal' => 40000,
            ],
            (object) [
                'tanggal' => '2026-10-13',
                'kode_transaksi' => 'TRX-20261013-001',
                'nama_kasir' => 'Siti Aminah',
                'jumlah_item' => 4,
                'total_qty' => 7,
                'subtotal' => 95000,
            ],
            (object) [
                'tanggal' => '2026-10-12',
                'kode_transaksi' => 'TRX-20261012-001',
                'nama_kasir' => 'Budi Santoso',
                'jumlah_item' => 3,
                'total_qty' => 5,
                'subtotal' => 65000,
            ],
        ];
    }
}

