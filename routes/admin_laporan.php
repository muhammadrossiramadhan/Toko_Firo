<?php

use App\Http\Controllers\Admin\LaporanPembelianController;
use App\Http\Controllers\Admin\LaporanPenjualanController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/laporan-penjualan', [LaporanPenjualanController::class, 'index'])->name('admin.laporan.penjualan');
    Route::get('/laporan-penjualan/pdf', [LaporanPenjualanController::class, 'exportPdf'])->name('admin.laporan.penjualan.pdf');
    Route::post('/laporan-penjualan/kirim-telegram', [LaporanPenjualanController::class, 'kirimTelegram'])->name('admin.laporan.kirim-telegram');

    Route::get('/laporan-pembelian', [LaporanPembelianController::class, 'index'])->name('admin.laporan.pembelian');
    Route::get('/laporan-pembelian/pdf', [LaporanPembelianController::class, 'exportPdf'])->name('admin.laporan.pembelian.pdf');
    Route::post('/laporan-pembelian/kirim-telegram', [LaporanPembelianController::class, 'kirimTelegram'])->name('admin.laporan.pembelian.kirim-telegram');

    Route::get('/laporan', [LaporanPenjualanController::class, 'index'])->name('admin.laporan');
});
