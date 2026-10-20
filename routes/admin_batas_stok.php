<?php

use App\Http\Controllers\Admin\BatasStokController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum merge ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/batas-stok', [BatasStokController::class, 'index'])->name('admin.batas-stok');
});

