<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Services\DashboardKasirService;
use Illuminate\Contracts\View\View;

class DashboardKasirController extends Controller
{
    public function __construct(
        protected DashboardKasirService $dashboardKasirService
    ) {}

    public function index(): View
    {
        return view('kasir.dashboard.index', [
            'kasir' => $this->dashboardKasirService->ringkasan(),
            'saldoHariIni' => $this->dashboardKasirService->saldoHariIni(),
            'stokMenipis' => $this->dashboardKasirService->stokMenipis(),
            'infoBarangBaru' => $this->dashboardKasirService->infoBarangBaru(),
        ]);
    }
}
