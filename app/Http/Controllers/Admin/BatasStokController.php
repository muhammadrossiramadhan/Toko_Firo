<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BatasStokController extends Controller
{
    /**
     * Menampilkan antarmuka Batas Stok Minimum (Figma P14).
     */
    public function index(Request $request): View
    {
        $daftarBatasStok = [
            (object) [
                'kode' => 'BRG-008',
                'nama' => 'Pensil 2B',
                'jenis' => 'ATK',
                'stok' => 0,
                'satuan' => 'pcs',
                'batas' => 3,
                'status' => 'Habis',
            ],
            (object) [
                'kode' => 'BRG-006',
                'nama' => 'Isi Staples No.10',
                'jenis' => 'ATK',
                'stok' => 2,
                'satuan' => 'box',
                'batas' => 3,
                'status' => 'Menipis',
            ],
            (object) [
                'kode' => 'BRG-003',
                'nama' => 'Kertas HVS A4 70gr',
                'jenis' => 'Fotocopy',
                'stok' => 4,
                'satuan' => 'rim',
                'batas' => 5,
                'status' => 'Menipis',
            ],
            (object) [
                'kode' => 'BRG-005',
                'nama' => 'Stapler HD-10',
                'jenis' => 'ATK',
                'stok' => 8,
                'satuan' => 'pcs',
                'batas' => 5,
                'status' => 'Normal',
            ],
            (object) [
                'kode' => 'BRG-001',
                'nama' => 'Pulpen Standard AE7',
                'jenis' => 'ATK',
                'stok' => 120,
                'satuan' => 'pcs',
                'batas' => 5,
                'status' => 'Aman',
            ],
        ];

        return view('admin.batas_stok.index', [
            'daftarBatasStok' => $daftarBatasStok,
        ]);
    }
}

