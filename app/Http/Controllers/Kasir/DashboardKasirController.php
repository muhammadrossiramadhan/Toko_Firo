<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DashboardKasirController extends Controller
{
    /**
     * Menampilkan antarmuka Dashboard Kasir (Frontend Prototype Figma Frame P05).
     */
    public function index(): View
    {
        $kasir = [
            'nama' => 'Rina Putri',
            'inisial' => 'R',
            'role' => 'Kasir',
            'tanggal' => 'Jumat, 3 Oktober 2026',
        ];

        $saldoHariIni = 'Rp 3.180.000';

        $stokMenipis = [
            [
                'nama' => 'Kertas HVS A4 70gr',
                'sisa' => 3,
                'status' => 'Menipis',
            ],
            [
                'nama' => 'Isi Staples No.10',
                'sisa' => 2,
                'status' => 'Menipis',
            ],
            [
                'nama' => 'Tinta Printer Canon',
                'sisa' => 1,
                'status' => 'Menipis',
            ],
        ];

        $infoBarangBaru = [
            [
                'deskripsi' => 'Spidol Snowman Hitam ditambahkan',
                'waktu' => '2 jam lalu',
            ],
            [
                'deskripsi' => 'Harga Buku Tulis Sidu berubah',
                'waktu' => '5 jam lalu',
            ],
            [
                'deskripsi' => 'Penghapus Faber-Castell diperbarui',
                'waktu' => '1 hari lalu',
            ],
        ];

        return view('kasir.dashboard.index', compact('kasir', 'saldoHariIni', 'stokMenipis', 'infoBarangBaru'));
    }
}

