<?php

use App\Http\Controllers\Admin\PembelianController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/pembelian', [PembelianController::class, 'index'])->name('admin.pembelian');
    Route::post('/pembelian', [PembelianController::class, 'store'])->name('admin.pembelian.store');
});

