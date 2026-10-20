<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Menampilkan antarmuka Profil Admin (Figma P18).
     */
    public function index(Request $request): View
    {
        $profil = [
            'inisial' => 'AF',
            'nama' => 'Ahmad Firo',
            'role' => 'Admin / Pemilik',
            'username' => 'admin_firo',
            'telepon' => '081234567890',
        ];

        return view('admin.profil.index', [
            'profil' => $profil,
        ]);
    }
}

