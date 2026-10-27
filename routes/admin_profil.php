<?php

use App\Http\Controllers\Admin\ProfilController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum rilis ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('admin.profil');
});

