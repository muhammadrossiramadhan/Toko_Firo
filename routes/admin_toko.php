<?php

use App\Http\Controllers\Admin\TokoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/pengaturan-toko', [TokoController::class, 'index'])->name('admin.toko.index');
    Route::match(['put', 'post'], '/pengaturan-toko', [TokoController::class, 'update'])->name('admin.toko.update');

    // Alias nama rute kompatibilitas sidebar & legacy tests
    Route::get('/pengaturan-toko-legacy', [TokoController::class, 'index'])->name('admin.toko');
});
