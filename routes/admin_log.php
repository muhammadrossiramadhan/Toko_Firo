<?php

use App\Http\Controllers\Admin\LogAktivitasController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum rilis ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('admin.log');
});

