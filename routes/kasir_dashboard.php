<?php

use App\Http\Controllers\Kasir\DashboardKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Dashboard Kasir (Frontend Prototype Figma Frame P05)
|--------------------------------------------------------------------------
| CATATAN: Untuk kebutuhan prototype frontend independen, middleware auth
| dan role:KASIR dinonaktifkan sementara. Pulihkan middleware ['auth', 'role:KASIR']
| sebelum integrasi produksi.
*/

Route::middleware(['web'])->group(function () {
    Route::get('/kasir/dashboard', [DashboardKasirController::class, 'index'])->name('kasir.dashboard');
});

