<?php

use App\Http\Controllers\Admin\LaporanPenjualanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:ADMIN'])->prefix('admin')->group(function () {
    Route::get('/laporan-penjualan', [LaporanPenjualanController::class, 'index'])->name('admin.laporan.penjualan');
});

