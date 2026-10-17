<?php

use App\Http\Controllers\Admin\BarangController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:ADMIN'])->prefix('admin')->group(function () {
    Route::get('/barang', [BarangController::class, 'index'])->name('admin.barang');
    Route::post('/barang', [BarangController::class, 'store'])->name('admin.barang.store');
    Route::put('/barang', [BarangController::class, 'update'])->name('admin.barang.update');
    Route::patch('/barang/harga', [BarangController::class, 'updateHarga'])->name('admin.barang.harga');
    Route::delete('/barang', [BarangController::class, 'destroy'])->name('admin.barang.destroy');
});
