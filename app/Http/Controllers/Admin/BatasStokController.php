<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BatasStokService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BatasStokController extends Controller
{
    public function __construct(
        protected BatasStokService $batasStokService
    ) {}

    /**
     * Menampilkan antarmuka Batas Stok Minimum (Figma P14).
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Penanganan aksi update jika dipanggil melalui query string atau method form
        if ($request->isMethod('post') || $request->has('action')) {
            if ($request->input('action') === 'update_semua' || $request->has('update_semua')) {
                return $this->updateSemua($request);
            }
            if ($request->input('action') === 'update_item' || $request->has('update_item') || $request->filled('kode_item')) {
                return $this->updateItem($request);
            }
        }

        $jenis = $request->query('jenis');
        $keyword = $request->query('q') ?? $request->query('keyword') ?? $request->query('cari');

        $items = $this->batasStokService->getItems($jenis, $keyword, 10);
        $jenisList = $this->batasStokService->getJenisList();

        return view('admin.batas_stok.index', [
            'daftarBatasStok' => $items,
            'items' => $items,
            'jenisList' => $jenisList,
            'filterJenis' => $jenis,
            'keyword' => $keyword,
        ]);
    }

    /**
     * Memperbarui batas stok minimum satu item secara individual.
     */
    public function updateItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_item' => ['required', 'string', 'exists:daftar_item,kode_item'],
            'batas_minimum' => ['required', 'integer', 'min:0', 'max:1000'],
        ], [
            'kode_item.required' => 'Kode item wajib diisi.',
            'kode_item.exists' => 'Item tidak ditemukan.',
            'batas_minimum.required' => 'Batas minimum wajib diisi.',
            'batas_minimum.integer' => 'Batas minimum harus berupa angka bulat.',
            'batas_minimum.min' => 'Batas minimum tidak boleh negatif.',
            'batas_minimum.max' => 'Batas minimum tidak boleh melebihi 1000.',
        ]);

        $idPelaku = auth()->id() ?? 1;

        $this->batasStokService->updateBatasItem(
            $validated['kode_item'],
            (int) $validated['batas_minimum'],
            $idPelaku
        );

        return redirect()->route('admin.batas-stok')
            ->with('success', 'Batas minimum item berhasil diperbarui.');
    }

    /**
     * Memperbarui batas stok minimum semua item non-jasa secara massal.
     */
    public function updateSemua(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'batas_minimum' => ['required', 'integer', 'min:0', 'max:1000'],
        ], [
            'batas_minimum.required' => 'Batas minimum wajib diisi.',
            'batas_minimum.integer' => 'Batas minimum harus berupa angka bulat.',
            'batas_minimum.min' => 'Batas minimum tidak boleh negatif.',
            'batas_minimum.max' => 'Batas minimum tidak boleh melebihi 1000.',
        ]);

        $idPelaku = auth()->id() ?? 1;

        $this->batasStokService->updateBatasSemua(
            (int) $validated['batas_minimum'],
            $idPelaku
        );

        return redirect()->route('admin.batas-stok')
            ->with('success', 'Batas minimum semua item berhasil diperbarui.');
    }
}
