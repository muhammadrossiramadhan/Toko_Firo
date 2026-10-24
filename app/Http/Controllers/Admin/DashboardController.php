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
        // TODO: menunggu keputusan integrasi nama login

        $ringkasan = $this->dashboardService->ringkasan();
        $penjualanMingguan = $this->dashboardService->penjualanMingguan();
        $topBarang = $this->dashboardService->topBarang();
        $aktivitasTerbaru = $this->dashboardService->aktivitasTerbaru();

        return view('admin.dashboard.index', [
            'ringkasan' => $ringkasan,
            'penjualanMingguan' => $penjualanMingguan,
            'topBarang' => $topBarang,
            'aktivitasTerbaru' => $aktivitasTerbaru,
        ]);
    }
}
