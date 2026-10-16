<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangController extends Controller
{
    /**
     * Menampilkan halaman kelola data barang (kerangka awal).
     */
    public function index(Request $request): View
    {
        return view('admin.barang.index', [
            'items' => [],
            'from' => 0,
            'to' => 0,
            'total' => 0,
            'currentPage' => 1,
            'totalPages' => 1,
            'pageElements' => [1],
        ]);
    }
}

