<?php

use App\Http\Controllers\Admin\PenggunaController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum merge ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('admin.pengguna.index');
    Route::post('/pengguna', [PenggunaController::class, 'store'])->name('admin.pengguna.store');
    Route::put('/pengguna/{id}', [PenggunaController::class, 'update'])->name('admin.pengguna.update');
    Route::match(['patch', 'put', 'post'], '/pengguna/{id}/status', [PenggunaController::class, 'toggleStatus'])->name('admin.pengguna.status');
    Route::match(['put', 'post'], '/pengguna/{id}/reset-password', [PenggunaController::class, 'resetPassword'])->name('admin.pengguna.reset-password');
    Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy'])->name('admin.pengguna.destroy');

    // Alias nama rute kompatibilitas sidebar & legacy tests
    Route::get('/pengguna-legacy', [PenggunaController::class, 'index'])->name('admin.pengguna');
});
