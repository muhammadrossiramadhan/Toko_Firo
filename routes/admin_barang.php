<?php

use App\Http\Controllers\Admin\BarangController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/barang')->group(function () {
    Route::get('/', [BarangController::class, 'index'])->name('admin.barang');
    Route::get('/riwayat', [BarangController::class, 'riwayat'])->name('admin.barang.riwayat');
    Route::post('/', [BarangController::class, 'store'])->name('admin.barang.store');
    Route::put('/', [BarangController::class, 'update'])->name('admin.barang.update');
    Route::patch('/harga', [BarangController::class, 'updateHarga'])->name('admin.barang.harga');
    Route::delete('/', [BarangController::class, 'destroy'])->name('admin.barang.destroy');
});
