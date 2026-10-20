<?php

use App\Http\Controllers\Admin\NotifikasiController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum rilis ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/pengaturan-notifikasi', [NotifikasiController::class, 'index'])->name('admin.notifikasi');
});

