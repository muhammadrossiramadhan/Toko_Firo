<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BackupController extends Controller
{
    /**
     * Menampilkan antarmuka Backup Data (Figma P14/P15).
     */
    public function index(Request $request): View
    {
        $riwayatBackup = [
            (object) [
                'waktu' => 'Hari ini, 14.30',
                'tipe' => 'Manual',
                'ukuran' => '2,4 MB',
                'status' => 'Berhasil',
            ],
            (object) [
                'waktu' => 'Kemarin, 22.00',
                'tipe' => 'Otomatis',
                'ukuran' => '2,4 MB',
                'status' => 'Berhasil',
            ],
            (object) [
                'waktu' => '1 Okt 2026, 22.00',
                'tipe' => 'Otomatis',
                'ukuran' => '2,3 MB',
                'status' => 'Berhasil',
            ],
            (object) [
                'waktu' => '30 Sep 2026, 22.00',
                'tipe' => 'Otomatis',
                'ukuran' => '2,3 MB',
                'status' => 'Berhasil',
            ],
        ];

        return view('admin.backup.index', [
            'riwayatBackup' => $riwayatBackup,
        ]);
    }
}

