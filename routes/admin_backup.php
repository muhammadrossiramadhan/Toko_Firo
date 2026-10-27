<?php

use App\Http\Controllers\Admin\BackupController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum rilis ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/backup', [BackupController::class, 'index'])->name('admin.backup');
    Route::post('/backup/buat', [BackupController::class, 'buat'])->name('admin.backup.buat');
    Route::post('/backup/jadwal', [BackupController::class, 'simpanJadwal'])->name('admin.backup.jadwal');
    Route::get('/backup/unduh/{filename}', [BackupController::class, 'unduh'])->name('admin.backup.unduh');
    Route::delete('/backup/{filename}', [BackupController::class, 'hapus'])->name('admin.backup.hapus');
});
