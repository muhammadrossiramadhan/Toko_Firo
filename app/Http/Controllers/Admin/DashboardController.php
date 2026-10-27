<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Menampilkan antarmuka Dashboard Admin (Figma P10).
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $namaUser = $user->nama ?? $user->name ?? $user->username ?? 'Admin';
        $role = strtoupper((string) ($user->role ?? 'ADMIN'));
        $roleUser = $role === 'ADMIN' ? 'Pemilik Toko' : 'Staf Kasir';

        // Ambil inisial avatar dinamis
        $words = array_values(array_filter(explode(' ', trim((string) $namaUser))));
        if (count($words) >= 2) {
            $inisialUser = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $inisialUser = strtoupper(substr((string) $namaUser, 0, min(2, strlen((string) $namaUser)))) ?: 'A';
        }

        $namaPanggilan = $words[0] ?? $namaUser;

        $ringkasan = $this->dashboardService->ringkasan();
        $penjualanMingguan = $this->dashboardService->penjualanMingguan();
        $topBarang = $this->dashboardService->topBarang();
        $aktivitasTerbaru = $this->dashboardService->aktivitasTerbaru();

        return view('admin.dashboard.index', [
            'ringkasan' => $ringkasan,
            'penjualanMingguan' => $penjualanMingguan,
            'topBarang' => $topBarang,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'namaUser' => $namaUser,
            'namaPanggilan' => $namaPanggilan,
            'inisialUser' => $inisialUser,
            'roleUser' => $roleUser,
        ]);
    }
}
