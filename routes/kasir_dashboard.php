<?php

use App\Http\Controllers\Kasir\DashboardKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Dashboard Kasir
|--------------------------------------------------------------------------
| Izin akses diberikan untuk peran kasir, admin, dan pemilik toko.
*/

Route::get('/kasir/dashboard', [DashboardKasirController::class, 'index'])->name('kasir.dashboard');
