<?php

use App\Http\Controllers\Admin\ProfilController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum rilis ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('admin.profil');
    Route::match(['put', 'post'], '/profil', [ProfilController::class, 'update'])->name('admin.profil.update');
    Route::match(['put', 'post'], '/profil/password', [ProfilController::class, 'updatePassword'])->name('admin.profil.password');
});
