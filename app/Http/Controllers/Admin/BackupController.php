<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        if ($request->isMethod('post') || $request->has('action')) {
            if ($request->input('action') === 'backup' || $request->isMethod('post')) {
                return $this->store($request);
            }
        }

        $riwayatBackup = $this->backupService->getRiwayatBackup(10);

        return view('admin.backup.index', [
            'riwayatBackup' => $riwayatBackup,
        ]);
    }

    /**
     * Menjalankan proses pencadangan database manual.
     */
    public function store(Request $request): RedirectResponse
    {
        $idPelaku = auth()->id() ?? 1;

        $hasil = $this->backupService->prosesBackup($idPelaku, 'MANUAL');

        return redirect()->route('admin.backup')
            ->with('success', 'Backup database berhasil dijalankan: ' . $hasil['nama_file']);
    }

    /**
     * Alias method untuk pembuatan backup.
     */
    public function createBackup(Request $request): RedirectResponse
    {
        return $this->store($request);
    }
}
