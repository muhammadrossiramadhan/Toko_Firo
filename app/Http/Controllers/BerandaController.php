<?php

namespace App\Http\Controllers;

use App\Models\DaftarItem;

class BerandaController extends Controller
{
    public function index()
    {
        $produkUnggulan = DaftarItem::where('tipe_item', '!=', 'JASA')
            ->where('stok', '>', 0)
            ->orderBy('harga_jual', 'desc')
            ->take(8)
            ->get();

        $jasaList = DaftarItem::where('tipe_item', 'JASA')->take(4)->get();

        return view('beranda', compact('produkUnggulan', 'jasaList'));
    }
}
