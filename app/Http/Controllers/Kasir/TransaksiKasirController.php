<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class TransaksiKasirController extends Controller
{
    /**
     * Menampilkan antarmuka wizard Transaksi Penjualan Kasir (Frontend Prototype Figma Frame P06).
     */
    public function index(): View
    {
        $katalog = [
            [
                'kode' => 'BRG-002',
                'nama' => 'Kertas HVS A4 70gr',
                'harga' => 52000,
                'stok' => 3,
                'satuan' => 'rim',
            ],
            [
                'kode' => 'BRG-003',
                'nama' => 'Pulpen Pilot G2',
                'harga' => 15000,
                'stok' => 24,
                'satuan' => 'pcs',
            ],
            [
                'kode' => 'BRG-004',
                'nama' => 'Buku Tulis Sidu 58hal',
                'harga' => 5500,
                'stok' => 40,
                'satuan' => 'buku',
            ],
            [
                'kode' => 'BRG-005',
                'nama' => 'Spidol Snowman Hitam',
                'harga' => 8000,
                'stok' => 12,
                'satuan' => 'pcs',
            ],
            [
                'kode' => 'BRG-006',
                'nama' => 'Isi Staples No.10',
                'harga' => 4500,
                'stok' => 2,
                'satuan' => 'kotak',
            ],
            [
                'kode' => 'BRG-007',
                'nama' => 'Map Plastik F4',
                'harga' => 3000,
                'stok' => 35,
                'satuan' => 'lembar',
            ],
        ];

        $keranjang = [
            [
                'kode' => 'BRG-002',
                'nama' => 'Kertas HVS A4 70gr',
                'harga' => 52000,
                'qty' => 1,
                'subtotal' => 52000,
            ],
            [
                'kode' => 'BRG-003',
                'nama' => 'Pulpen Pilot G2',
                'harga' => 15000,
                'qty' => 2,
                'subtotal' => 30000,
            ],
        ];

        $totalBelanja = 82000;

        $kasir = [
            'nama' => 'Rina Putri',
            'inisial' => 'R',
            'role' => 'Kasir',
        ];

        return view('kasir.transaksi.index', compact('katalog', 'keranjang', 'totalBelanja', 'kasir'));
    }
}

