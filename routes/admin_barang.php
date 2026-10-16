<?php

use App\Http\Controllers\Admin\BarangController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:ADMIN'])->prefix('admin')->group(function () {
    Route::get('/barang', [BarangController::class, 'index'])->name('admin.barang');
});

