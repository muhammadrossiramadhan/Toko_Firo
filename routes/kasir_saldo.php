<?php

use App\Http\Controllers\Kasir\SaldoKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Saldo Harian (Frontend Prototype Figma Frame P08)
|--------------------------------------------------------------------------
| CATATAN: Untuk kebutuhan prototype frontend independen, middleware auth
| dan role:KASIR dinonaktifkan sementara. Pulihkan middleware ['auth', 'role:KASIR']
| sebelum integrasi final.
*/

Route::middleware(['web'])->group(function () {
    Route::get('/kasir/saldo', [SaldoKasirController::class, 'index'])->name('kasir.saldo');
});

