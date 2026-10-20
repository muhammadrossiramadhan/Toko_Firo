<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class StokKasirController extends Controller
{
    /**
     * Menampilkan antarmuka Lihat Stok Barang Kasir (Frontend Prototype Figma Frame P07).
     */
    public function index(): View
    {
        $ringkasan = [
            'semua' => 47,
            'habis' => 2,
            'menipis' => 5,
            'normal' => 28,
            'aman' => 12,
        ];

        $daftarBarang = [
            [
                'kode' => 'BRG-007',
                'nama' => 'Pensil 2B Faber',
                'jenis' => 'ATK',
                'merek' => 'Faber-Castell',
                'stok' => 0,
                'satuan' => 'pcs',
                'harga' => 'Rp 5.000',
                'status' => 'Habis',
            ],
            [
                'kode' => 'BRG-012',
                'nama' => 'Lem Kertas UHU',
                'jenis' => 'ATK',
                'merek' => 'UHU',
                'stok' => 1,
                'satuan' => 'pcs',
                'harga' => 'Rp 9.000',
                'status' => 'Habis',
            ],
            [
                'kode' => 'BRG-006',
                'nama' => 'Isi Staples No.10',
                'jenis' => 'ATK',
                'merek' => 'Kangaro',
                'stok' => 2,
                'satuan' => 'box',
                'harga' => 'Rp 4.500',
                'status' => 'Menipis',
            ],
            [
                'kode' => 'BRG-002',
                'nama' => 'Kertas HVS A4 70gr',
                'jenis' => 'ATK',
                'merek' => 'Sinar Dunia',
                'stok' => 3,
                'satuan' => 'rim',
                'harga' => 'Rp 52.000',
                'status' => 'Menipis',
            ],
            [
                'kode' => 'BRG-003',
                'nama' => 'Tinta Printer Canon',
                'jenis' => 'ATK',
                'merek' => 'Canon',
                'stok' => 1,
                'satuan' => 'botol',
                'harga' => 'Rp 90.000',
                'status' => 'Menipis',
            ],
            [
                'kode' => 'BRG-008',
                'nama' => 'Spidol Snowman',
                'jenis' => 'ATK',
                'merek' => 'Snowman',
                'stok' => 12,
                'satuan' => 'pcs',
                'harga' => 'Rp 8.000',
                'status' => 'Normal',
            ],
            [
                'kode' => 'BRG-001',
                'nama' => 'Pulpen Pilot G2',
                'jenis' => 'ATK',
                'merek' => 'Pilot',
                'stok' => 24,
                'satuan' => 'pcs',
                'harga' => 'Rp 15.000',
                'status' => 'Aman',
            ],
            [
                'kode' => 'BRG-004',
                'nama' => 'Buku Tulis Sidu',
                'jenis' => 'ATK',
                'merek' => 'Sidu',
                'stok' => 40,
                'satuan' => 'pcs',
                'harga' => 'Rp 5.500',
                'status' => 'Aman',
            ],
        ];

        $kasir = [
            'nama' => 'Rina Putri',
            'inisial' => 'R',
            'role' => 'Kasir',
        ];

        return view('kasir.stok.index', compact('ringkasan', 'daftarBarang', 'kasir'));
    }
}

