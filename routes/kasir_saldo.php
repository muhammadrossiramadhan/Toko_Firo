<?php

use App\Http\Controllers\Kasir\SaldoKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Saldo Harian
|--------------------------------------------------------------------------
| Izin akses diberikan untuk peran kasir, admin, dan pemilik toko.
*/

Route::middleware(['web', 'auth', 'role:kasir,admin,pemilik'])->group(function () {
    Route::get('/kasir/saldo', [SaldoKasirController::class, 'index'])->name('kasir.saldo');
});
