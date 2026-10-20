<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan antarmuka Dashboard Admin (Figma P10).
     */
    public function index(Request $request): View
    {
        $ringkasan = [
            'penjualan_hari_ini' => [
                'label' => 'Penjualan Hari Ini',
                'nilai' => 'Rp 3.180.000',
                'badge' => '+12%',
                'tipe' => 'positif',
            ],
            'jumlah_transaksi' => [
                'label' => 'Jumlah Transaksi',
                'nilai' => '14',
                'badge' => '+3',
                'tipe' => 'positif',
            ],
            'stok_menipis' => [
                'label' => 'Stok Menipis',
                'nilai' => '7',
                'subteks' => '2 habis',
                'tipe' => 'peringatan',
            ],
            'laba_hari_ini' => [
                'label' => 'Laba Hari Ini',
                'nilai' => 'Rp 680.000',
                'badge' => '+8%',
                'tipe' => 'positif',
            ],
        ];

        $penjualanMingguan = [
            [
                'hari' => 'Sen',
                'persen' => 45,
                'is_puncak' => false,
                'nominal' => 'Rp 1.450.000',
            ],
            [
                'hari' => 'Sel',
                'persen' => 30,
                'is_puncak' => false,
                'nominal' => 'Rp 950.000',
            ],
            [
                'hari' => 'Rab',
                'persen' => 60,
                'is_puncak' => false,
                'nominal' => 'Rp 1.900.000',
            ],
            [
                'hari' => 'Kam',
                'persen' => 40,
                'is_puncak' => false,
                'nominal' => 'Rp 1.250.000',
            ],
            [
                'hari' => 'Jum',
                'persen' => 100,
                'is_puncak' => true,
                'nominal' => 'Rp 3.180.000',
            ],
            [
                'hari' => 'Sab',
                'persen' => 20,
                'is_puncak' => false,
                'nominal' => 'Rp 650.000',
            ],
            [
                'hari' => 'Min',
                'persen' => 15,
                'is_puncak' => false,
                'nominal' => 'Rp 480.000',
            ],
        ];

        $topBarang = [
            (object) [
                'nomor' => 1,
                'nama' => 'Kertas HVS A4',
                'terjual' => '8 terjual',
                'kategori' => 'Kertas & Cetak',
            ],
            (object) [
                'nomor' => 2,
                'nama' => 'Pulpen Pilot G2',
                'terjual' => '6 terjual',
                'kategori' => 'Alat Tulis',
            ],
            (object) [
                'nomor' => 3,
                'nama' => 'Fotocopy A4',
                'terjual' => '5 layanan',
                'kategori' => 'Jasa Layanan',
            ],
        ];

        $aktivitasTerbaru = [
            (object) [
                'waktu' => '14:32',
                'keterangan' => 'Rina menyelesaikan transaksi TRX-001',
                'tipe' => 'transaksi',
            ],
            (object) [
                'waktu' => '13:50',
                'keterangan' => 'Stok Tinta Canon habis',
                'tipe' => 'peringatan',
            ],
            (object) [
                'waktu' => '12:00',
                'keterangan' => 'Deni login sebagai kasir',
                'tipe' => 'autentikasi',
            ],
            (object) [
                'waktu' => '10:30',
                'keterangan' => 'Admin menambahkan Spidol Snowman',
                'tipe' => 'master_data',
            ],
        ];

        return view('admin.dashboard.index', [
            'ringkasan' => $ringkasan,
            'penjualanMingguan' => $penjualanMingguan,
            'topBarang' => $topBarang,
            'aktivitasTerbaru' => $aktivitasTerbaru,
        ]);
    }
}

