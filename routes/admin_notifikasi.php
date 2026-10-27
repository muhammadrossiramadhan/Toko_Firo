<?php

use App\Http\Controllers\Admin\NotifikasiController;
use Illuminate\Support\Facades\Route;

// TODO WAJIB sebelum rilis ke staging: pulihkan middleware 'auth' dan 'role:ADMIN'.
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/pengaturan-notifikasi', [NotifikasiController::class, 'index'])->name('admin.notifikasi.index');
    Route::post('/pengaturan-notifikasi/test', [NotifikasiController::class, 'testKoneksi'])->name('admin.notifikasi.test');
    Route::post('/pengaturan-notifikasi/simpan', [NotifikasiController::class, 'simpan'])->name('admin.notifikasi.simpan');
});

// Alias nama rute untuk kompatibilitas tautan sidebar
Route::middleware(['web'])->get('/admin/pengaturan-notifikasi-alias', [NotifikasiController::class, 'index'])->name('admin.notifikasi');

