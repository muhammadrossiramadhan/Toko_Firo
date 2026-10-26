<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TokoController extends Controller
{
    /**
     * Menampilkan antarmuka Pengaturan Toko & Struk (Figma P17/P18).
     */
    public function index(Request $request): View
    {
        $infoToko = [
            'nama' => 'Toko Firo',
            'alamat' => 'Jl. Contoh No. 123, Kota',
            'telepon' => '081234567890',
        ];

        $pengaturanStruk = [
            'header' => 'TOKO FIRO — ATK & Fotocopy',
            'footer' => 'Terima kasih telah berbelanja!',
        ];

        $whatsappBot = [
            'status' => 'Aktif',
            'terhubung' => '08123456789',
        ];

        return view('admin.toko.index', [
            'infoToko' => $infoToko,
            'pengaturanStruk' => $pengaturanStruk,
            'whatsappBot' => $whatsappBot,
        ]);
    }
}

