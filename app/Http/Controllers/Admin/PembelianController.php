<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembelianController extends Controller
{
    /**
     * Menampilkan halaman Pembelian Barang (Figma P21).
     */
    public function index(Request $request): View
    {
        $barangRestock = [
            (object) [
                'nama' => 'Pensil 2B',
                'stok' => 0,
                'satuan' => 'pcs',
                'status' => 'Habis',
            ],
            (object) [
                'nama' => 'Isi Staples No.10',
                'stok' => 2,
                'satuan' => 'box',
                'status' => 'Menipis',
            ],
            (object) [
                'nama' => 'Kertas HVS A4',
                'stok' => 4,
                'satuan' => 'rim',
                'status' => 'Menipis',
            ],
        ];

        $riwayatPembelian = [
            (object) [
                'waktu' => '2026-10-18 14:30',
                'kode' => 'PB-20261018-001',
                'barang' => 'Kertas HVS A4',
                'jumlah' => '10 rim',
                'total' => 550000,
                'status' => 'Selesai',
            ],
            (object) [
                'waktu' => '2026-10-17 10:15',
                'kode' => 'PB-20261017-001',
                'barang' => 'Pulpen Faster C600',
                'jumlah' => '5 pak',
                'total' => 175000,
                'status' => 'Selesai',
            ],
            (object) [
                'waktu' => '2026-10-15 16:45',
                'kode' => 'PB-20261015-001',
                'barang' => 'Buku Tulis Sinar Dunia 38',
                'jumlah' => '20 pak',
                'total' => 760000,
                'status' => 'Selesai',
            ],
            (object) [
                'waktu' => '2026-10-12 11:20',
                'kode' => 'PB-20261012-001',
                'barang' => 'Tinta Printer Epson 003 Black',
                'jumlah' => '3 botol',
                'total' => 285000,
                'status' => 'Selesai',
            ],
        ];

        return view('admin.pembelian.index', [
            'barangRestock' => $barangRestock,
            'riwayatPembelian' => $riwayatPembelian,
        ]);
    }
}

