<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class InfoBarangKasirController extends Controller
{
    /**
     * Menampilkan antarmuka Info Barang Baru Kasir (Frontend Prototype Figma Frame P09).
     */
    public function index(): View
    {
        $kategori = [
            'Semua',
            'Barang Baru',
            'Perubahan Harga',
            'Stok Diperbarui',
        ];

        $daftarPembaruan = [
            [
                'tipe' => 'baru',
                'badge' => 'Baru',
                'inisial' => 'NEW',
                'nama' => 'Spidol Snowman Hitam',
                'keterangan' => 'Ditambahkan ke katalog',
                'detail' => 'Harga: Rp 8.000 · Stok awal: 12',
                'waktu' => '2 jam lalu',
            ],
            [
                'tipe' => 'harga',
                'badge' => 'Harga',
                'inisial' => 'Rp',
                'nama' => 'Buku Tulis Sidu 58hal',
                'keterangan' => 'Harga diperbarui',
                'detail' => 'Rp 5.000 -> Rp 5.500',
                'waktu' => '5 jam lalu',
            ],
            [
                'tipe' => 'stok',
                'badge' => 'Update',
                'inisial' => 'STK',
                'nama' => 'Penghapus Faber-Castell',
                'keterangan' => 'Stok diperbarui oleh Admin',
                'detail' => 'Stok: 15 -> 18',
                'waktu' => '1 hari lalu',
            ],
            [
                'tipe' => 'baru',
                'badge' => 'Baru',
                'inisial' => 'NEW',
                'nama' => 'Amplop Putih Polos',
                'keterangan' => 'Ditambahkan ke katalog',
                'detail' => 'Harga: Rp 500 · Stok awal: 200',
                'waktu' => '1 hari lalu',
            ],
            [
                'tipe' => 'harga',
                'badge' => 'Harga',
                'inisial' => 'Rp',
                'nama' => 'Tinta Printer Canon',
                'keterangan' => 'Harga diperbarui',
                'detail' => 'Rp 85.000 -> Rp 90.000',
                'waktu' => '2 hari lalu',
            ],
        ];

        $kasir = [
            'nama' => 'Rina Putri',
            'inisial' => 'R',
            'role' => 'Kasir',
        ];

        return view('kasir.info-barang.index', compact('kategori', 'daftarPembaruan', 'kasir'));
    }
}

