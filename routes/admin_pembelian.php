<?php

use App\Http\Controllers\Admin\PembelianController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/pembelian', [PembelianController::class, 'index'])->name('admin.pembelian');
    Route::get('/pembelian/index', [PembelianController::class, 'index'])->name('admin.pembelian.index');
    Route::post('/pembelian', [PembelianController::class, 'store'])->name('admin.pembelian.store');
});

