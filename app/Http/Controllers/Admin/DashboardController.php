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

        // Hitung puncak penjualan mingguan secara dinamis
        $puncak = collect($penjualanMingguan)->firstWhere('is_puncak', true);

        // Hitung notifikasi belum dibaca / log notifikasi terbaru
        $notifCount = \Illuminate\Support\Facades\DB::table('log_notifikasi')->where('status', 'MENUNGGU')->count();

        // Format tanggal hari ini dalam bahasa Indonesia
        $daftarHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $daftarBulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $tanggalHariIni = $daftarHari[date('w')] . ', ' . date('j') . ' ' . $daftarBulan[(int) date('n')] . ' ' . date('Y');

        return view('admin.dashboard.index', [
            'ringkasan' => $ringkasan,
            'penjualanMingguan' => $penjualanMingguan,
            'topBarang' => $topBarang,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'namaUser' => $namaUser,
            'namaPanggilan' => $namaPanggilan,
            'inisialUser' => $inisialUser,
            'roleUser' => $roleUser,
            'puncakPenjualan' => $puncak,
            'notifCount' => $notifCount,
            'tanggalHariIni' => $tanggalHariIni,
        ]);
    }
}
