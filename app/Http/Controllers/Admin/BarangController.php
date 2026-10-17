<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemBarang;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar barang dari database dengan paginasi, pencarian, dan filter.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $jenis = (string) $request->query('jenis', 'Semua');
        $urut = (string) $request->query('urut', 'terbaru');

        $query = ItemBarang::query();

        // Pencarian (q): cocok sebagian pada kode_item atau nama_item
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kode_item', 'like', "%{$q}%")
                  ->orWhere('nama_item', 'like', "%{$q}%");
            });
        }

        // Filter Jenis
        if ($jenis !== '' && $jenis !== 'Semua') {
            $query->where('jenis', $jenis);
        }

        // Pengurutan
        if ($urut === 'nama') {
            $query->orderBy('nama_item', 'asc');
        } elseif ($urut === 'stok') {
            $query->orderBy('stok', 'asc');
        } else {
            // Default: terbaru (dibuat_pada menurun)
            $query->orderBy('dibuat_pada', 'desc');
        }

        $items = $query->paginate(7)->withQueryString();

        $daftarJenis = ItemBarang::query()
            ->whereNotNull('jenis')
            ->where('jenis', '!=', '')
            ->distinct()
            ->orderBy('jenis', 'asc')
            ->pluck('jenis');

        return view('admin.barang.index', [
            'items' => $items,
            'daftarJenis' => $daftarJenis,
            'q' => $q,
            'jenis' => $jenis,
            'urut' => $urut,
        ]);
    }
}
