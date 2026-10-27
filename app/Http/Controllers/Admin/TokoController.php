<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PengaturanTokoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TokoController extends Controller
{
    public function __construct(
        protected PengaturanTokoService $pengaturanService
    ) {}

    /**
     * Menampilkan antarmuka Pengaturan Toko & Struk (Figma P17/P18).
     */
    public function index(Request $request): View
    {
        $pengaturan = $this->pengaturanService->getSettings();

        $infoToko = [
            'nama' => $pengaturan['nama_toko'],
            'alamat' => $pengaturan['alamat'],
            'telepon' => $pengaturan['telepon'],
        ];

        $pengaturanStruk = [
            'header' => $pengaturan['header_struk'],
            'footer' => $pengaturan['footer_struk'],
        ];

        $whatsappBot = [
            'status' => 'Nonaktif',
            'keterangan' => $pengaturan['whatsapp_status'] ?? 'Nonaktif (Belum Dikonfigurasi)',
            'terhubung' => 'Belum Terhubung',
        ];

        return view('admin.toko.index', [
            'pengaturan' => $pengaturan,
            'infoToko' => $infoToko,
            'pengaturanStruk' => $pengaturanStruk,
            'whatsappBot' => $whatsappBot,
        ]);
    }

    /**
     * Memperbarui informasi toko dan teks identitas struk secara persisten.
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nama_toko'    => ['required', 'string', 'max:100'],
            'alamat'       => ['required', 'string'],
            'telepon'      => ['required', 'string', 'max:20'],
            'header_struk' => ['nullable', 'string', 'max:255'],
            'footer_struk' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_toko.required' => 'Nama toko wajib diisi.',
            'alamat.required'    => 'Alamat toko wajib diisi.',
            'telepon.required'   => 'Nomor telepon toko wajib diisi.',
        ]);

        $saved = $this->pengaturanService->updateSettings($validated);

        // Catat ke log aktivitas jika pengguna terautentikasi
        try {
            $userId = auth()->id() ?? auth()->user()?->id_pengguna;
            DB::table('log_aktivitas')->insert([
                'waktu' => now(),
                'keterangan' => 'Pembaruan identitas profil dan pengaturan struk toko.',
                'tipe_aksi' => 'PENGATURAN_TOKO',
                'id_pengguna' => $userId,
            ]);
        } catch (\Throwable $e) {
            // Abaikan kesalahan log audit
        }

        $pesanSukses = 'Pengaturan toko dan struk berhasil diperbarui.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $pesanSukses,
                'data' => $saved,
            ], 200);
        }

        return redirect()->route('admin.toko.index')->with('success', $pesanSukses);
    }
}
