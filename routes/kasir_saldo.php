<?php

use App\Http\Controllers\Kasir\SaldoKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Saldo Harian
|--------------------------------------------------------------------------
| Izin akses diberikan untuk peran kasir, admin, dan pemilik toko.
*/

Route::get('/kasir/saldo', [SaldoKasirController::class, 'index'])->name('kasir.saldo');
Route::get('/kasir/saldo/pdf', [SaldoKasirController::class, 'exportPdf'])->name('kasir.saldo.pdf');
Route::post('/kasir/saldo/telegram', [SaldoKasirController::class, 'kirimTelegram'])->name('kasir.saldo.telegram');
