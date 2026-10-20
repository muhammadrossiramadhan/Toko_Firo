<?php

namespace App\Http\Controllers;

use App\Models\DaftarItem;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = DaftarItem::query();

        // Filter by jenis (category) if provided
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Search by name
        if ($request->filled('cari')) {
            $query->where('nama_item', 'like', '%' . $request->cari . '%');
        }

        // Order: in-stock first, then by name
        $query->orderByRaw('stok = 0 ASC')->orderBy('nama_item');

        $items = $query->paginate(12)->withQueryString();

        // Get distinct categories for filter pills
        $kategori = DaftarItem::whereNotNull('jenis')
            ->distinct()
            ->orderBy('jenis')
            ->pluck('jenis');

        // Get last update time from log_aktivitas or daftar_transaksi
        $lastUpdate = LogAktivitas::orderByDesc('waktu')->value('waktu');
        if (!$lastUpdate) {
            // fallback: check daftar_transaksi
            $lastUpdate = \App\Models\DaftarTransaksi::orderByDesc('tanggal_log')->value('tanggal_log');
        }

        return view('katalog', compact('items', 'kategori', 'lastUpdate'));
    }
}
