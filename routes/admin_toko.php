<?php

use App\Http\Controllers\Admin\TokoController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum rilis ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/pengaturan-toko', [TokoController::class, 'index'])->name('admin.toko');
});

