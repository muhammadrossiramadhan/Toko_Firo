<?php

use App\Http\Controllers\Admin\LaporanPenjualanController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum merge ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/laporan-penjualan', [LaporanPenjualanController::class, 'index'])->name('admin.laporan.penjualan');
    Route::post('/laporan-penjualan/kirim-telegram', [LaporanPenjualanController::class, 'kirimTelegram'])->name('admin.laporan.kirim-telegram');
});
