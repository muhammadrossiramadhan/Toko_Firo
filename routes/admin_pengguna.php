<?php

use App\Http\Controllers\Admin\PenggunaController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum merge ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('admin.pengguna');
});

