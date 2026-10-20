<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    /**
     * Menampilkan halaman kelola akun pengguna (Figma P13).
     */
    public function index(Request $request): View
    {
        $pengguna = [
            (object) [
                'nama' => 'Junaidi Saleh',
                'username' => 'junaidi',
                'role' => 'ADMIN',
                'status' => 'AKTIF',
                'inisial' => 'J',
                'login_terakhir' => '03 Okt 2026, 08:15',
            ],
            (object) [
                'nama' => 'Rina Putri',
                'username' => 'rina',
                'role' => 'KASIR',
                'status' => 'AKTIF',
                'inisial' => 'R',
                'login_terakhir' => '03 Okt 2026, 07:30',
            ],
            (object) [
                'nama' => 'Siti Aminah',
                'username' => 'siti',
                'role' => 'KASIR',
                'status' => 'NONAKTIF',
                'inisial' => 'S',
                'login_terakhir' => '28 Sep 2026, 14:00',
            ],
        ];

        return view('admin.pengguna.index', [
            'pengguna' => $pengguna,
        ]);
    }
}

