<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BatasStokService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BatasStokController extends Controller
{
    public function __construct(
        protected BatasStokService $batasStokService
    ) {}

    /**
     * Menampilkan antarmuka Batas Stok Minimum (Figma P14).
     * Murni metode GET hanya untuk membaca data, tidak menangani mutasi state (SEC-06).
     */
    public function index(Request $request): View
    {
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
     * Menyimpan perubahan batas stok minimum via POST/PATCH (SEC-06).
     */
    public function simpan(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $idPelaku = auth()->id() ?? 1;

        // Mode 1: Update massal semua item ke satu nilai
        if ($request->has('update_semua') || $request->input('mode') === 'semua' || $request->has('atur_semua')) {
            $validated = $request->validate([
                'batas_minimum' => ['required', 'integer', 'min:0', 'max:1000'],
            ], [
                'batas_minimum.required' => 'Batas minimum wajib diisi.',
                'batas_minimum.integer' => 'Batas minimum harus berupa angka bulat.',
                'batas_minimum.min' => 'Batas minimum tidak boleh negatif.',
                'batas_minimum.max' => 'Batas minimum tidak boleh melebihi 1000.',
            ]);

            $this->batasStokService->updateBatasSemua((int) $validated['batas_minimum'], $idPelaku);
            DB::table('daftar_item')->where('tipe_item', '!=', 'JASA')->update(['batas_minimum' => (int) $validated['batas_minimum']]);

            $pesan = 'Batas minimum semua item berhasil diperbarui.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'success', 'message' => $pesan]);
            }
            return redirect()->route('admin.batas-stok')->with('success', $pesan);
        }

        // Mode 2: Update satu item (request langsung kode_item dan batas_minimum)
        if ($request->filled('kode_item')) {
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

            $kodeItem = (string) $validated['kode_item'];
            $batas = (int) $validated['batas_minimum'];

            $this->batasStokService->updateBatasItem($kodeItem, $batas, $idPelaku);
            DB::table('daftar_item')->where('kode_item', $kodeItem)->update(['batas_minimum' => $batas]);

            $pesan = 'Batas minimum item berhasil diperbarui.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'success', 'message' => $pesan, 'kode_item' => $kodeItem, 'batas_minimum' => $batas]);
            }
            return redirect()->route('admin.batas-stok')->with('success', $pesan);
        }

        // Mode 3: Update batch per-item (items: ['BRG-01' => 10] atau items: [['kode_item' => 'BRG-01', 'batas_minimum' => 10]])
        if ($request->has('items') && is_array($request->input('items'))) {
            $items = $request->input('items');
            foreach ($items as $key => $val) {
                if (is_array($val)) {
                    $kode = (string) ($val['kode_item'] ?? $val['kode'] ?? $key);
                    $batas = $val['batas_minimum'] ?? $val['batas'] ?? $val['nilai'] ?? null;
                } else {
                    $kode = (string) $key;
                    $batas = $val;
                }

                if ($kode !== '' && is_numeric($batas) && (int) $batas >= 0 && (int) $batas <= 1000) {
                    $this->batasStokService->updateBatasItem($kode, (int) $batas, $idPelaku);
                    DB::table('daftar_item')->where('kode_item', $kode)->update(['batas_minimum' => (int) $batas]);
                }
            }

            $pesan = 'Perubahan batas stok minimum berhasil disimpan.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'success', 'message' => $pesan]);
            }
            return redirect()->route('admin.batas-stok')->with('success', $pesan);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'error', 'message' => 'Format data tidak valid.'], 422);
        }

        return redirect()->route('admin.batas-stok');
    }

    /**
     * Memperbarui batas stok minimum satu item secara individual.
     */
    public function updateItem(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
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
        $kodeItem = (string) $validated['kode_item'];
        $batas = (int) $validated['batas_minimum'];

        $this->batasStokService->updateBatasItem(
            $kodeItem,
            $batas,
            $idPelaku
        );
        DB::table('daftar_item')->where('kode_item', $kodeItem)->update(['batas_minimum' => $batas]);

        $pesan = 'Batas minimum item berhasil diperbarui.';
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'message' => $pesan]);
        }

        return redirect()->route('admin.batas-stok')
            ->with('success', $pesan);
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
