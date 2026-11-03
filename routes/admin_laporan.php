<?php

use App\Http\Controllers\Admin\LaporanPenjualanController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/laporan-penjualan', [LaporanPenjualanController::class, 'index'])->name('admin.laporan.penjualan');
    Route::get('/laporan', [LaporanPenjualanController::class, 'index'])->name('admin.laporan');
    Route::post('/laporan-penjualan/kirim-telegram', [LaporanPenjualanController::class, 'kirimTelegram'])->name('admin.laporan.kirim-telegram');
});
