<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService
    ) {}

    /**
     * Menampilkan antarmuka Backup Data (Figma P14/P15).
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Kompatibilitas jika form lama POST ke index
        if ($request->isMethod('post') || $request->input('action') === 'backup') {
            return $this->buat($request);
        }

        $riwayatPaginator = $this->backupService->getRiwayatBackup(10);
        $riwayatMetadata = $this->backupService->getRiwayat();

        return view('admin.backup.index', [
            'riwayatBackup' => $riwayatPaginator,
            'riwayatList'   => $riwayatMetadata['items'] ?? [],
            'totalFile'     => $riwayatMetadata['total_file'] ?? $riwayatPaginator->total(),
            'totalUkuran'   => $riwayatMetadata['total_ukuran'] ?? '0 KB',
            'terakhir'      => $riwayatMetadata['terakhir'] ?? '-',
        ]);
    }

    /**
     * Menjalankan proses pencadangan database.
     */
    public function buat(Request $request): JsonResponse|RedirectResponse
    {
        $tipe = (string) $request->input('tipe', 'Manual');

        try {
            $hasil = $this->backupService->buatBackup($tipe);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Cadangan database berhasil dibuat: ' . $hasil['nama_file'],
                    'data' => $hasil,
                ], 200);
            }

            return redirect()->route('admin.backup')
                ->with('success', 'Backup database berhasil dijalankan: ' . $hasil['nama_file']);
        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal membuat cadangan database: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('admin.backup')
                ->with('error', 'Gagal membuat cadangan database: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh berkas cadangan database.
     */
    public function unduh(string $filename): BinaryFileResponse
    {
        return $this->backupService->unduhBackup($filename);
    }

    /**
     * Menghapus berkas cadangan database.
     */
    public function hapus(string $filename, Request $request): JsonResponse|RedirectResponse
    {
        $sukses = $this->backupService->hapusBackup($filename);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => $sukses ? 'success' : 'error',
                'message' => $sukses ? 'Berkas cadangan berhasil dihapus.' : 'Gagal menghapus berkas cadangan.',
            ], $sukses ? 200 : 400);
        }

        return redirect()->route('admin.backup')
            ->with($sukses ? 'success' : 'error', $sukses ? 'Berkas cadangan berhasil dihapus.' : 'Gagal menghapus berkas cadangan.');
    }

    /**
     * Alias method store untuk pembuatan backup (kompatibilitas).
     */
    public function store(Request $request): RedirectResponse
    {
        $res = $this->buat($request);
        return $res instanceof RedirectResponse ? $res : redirect()->route('admin.backup');
    }

    /**
     * Alias method createBackup.
     */
    public function createBackup(Request $request): RedirectResponse
    {
        return $this->store($request);
    }
}
