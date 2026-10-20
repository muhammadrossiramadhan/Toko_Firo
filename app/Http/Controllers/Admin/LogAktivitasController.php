<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogAktivitasController extends Controller
{
    /**
     * Menampilkan antarmuka Log Aktivitas (Figma P16).
     */
    public function index(Request $request): View
    {
        $daftarLog = [
            (object) [
                'waktu' => '03/10/26 14:32',
                'user' => 'Rina Putri',
                'tipe' => 'Transaksi',
                'detail' => 'Menyelesaikan TRX-261003-001 — Rp 82.000',
            ],
            (object) [
                'waktu' => '03/10/26 14:30',
                'user' => 'Rina Putri',
                'tipe' => 'Transaksi',
                'detail' => 'Membuat transaksi baru TRX-261003-001',
            ],
            (object) [
                'waktu' => '03/10/26 13:50',
                'user' => 'Sistem',
                'tipe' => 'Stok',
                'detail' => 'Stok Tinta Canon mencapai 0 (habis)',
            ],
            (object) [
                'waktu' => '03/10/26 13:15',
                'user' => 'Rina Putri',
                'tipe' => 'Transaksi',
                'detail' => 'Menyelesaikan TRX-261003-002 — Rp 25.000',
            ],
            (object) [
                'waktu' => '03/10/26 12:40',
                'user' => 'Deni Saputra',
                'tipe' => 'Transaksi',
                'detail' => 'Menyelesaikan TRX-261003-003 — Rp 22.500',
            ],
            (object) [
                'waktu' => '03/10/26 12:00',
                'user' => 'Deni Saputra',
                'tipe' => 'Login',
                'detail' => 'Login sebagai kasir',
            ],
            (object) [
                'waktu' => '03/10/26 10:30',
                'user' => 'Junaidi Saleh',
                'tipe' => 'Barang',
                'detail' => 'Menambahkan Spidol Snowman Hitam',
            ],
            (object) [
                'waktu' => '03/10/26 10:15',
                'user' => 'Junaidi Saleh',
                'tipe' => 'Harga',
                'detail' => 'Mengubah harga Buku Sidu: Rp 5.000 → Rp 5.500',
            ],
            (object) [
                'waktu' => '03/10/26 08:00',
                'user' => 'Rina Putri',
                'tipe' => 'Login',
                'detail' => 'Login sebagai kasir',
            ],
            (object) [
                'waktu' => '03/10/26 07:50',
                'user' => 'Junaidi Saleh',
                'tipe' => 'Modal',
                'detail' => 'Set modal awal Rp 500.000 untuk semua kasir',
            ],
        ];

        return view('admin.log.index', [
            'daftarLog' => $daftarLog,
        ]);
    }
}

