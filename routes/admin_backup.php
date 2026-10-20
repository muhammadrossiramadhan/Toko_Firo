<?php

use App\Http\Controllers\Admin\BackupController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum rilis ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/backup', [BackupController::class, 'index'])->name('admin.backup');
});

