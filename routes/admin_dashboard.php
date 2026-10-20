<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum rilis ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});

