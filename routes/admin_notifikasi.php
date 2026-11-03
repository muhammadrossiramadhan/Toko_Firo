<?php

use App\Http\Controllers\Admin\NotifikasiController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/pengaturan-notifikasi', [NotifikasiController::class, 'index'])->name('admin.notifikasi.index');
    Route::post('/pengaturan-notifikasi/test', [NotifikasiController::class, 'testKoneksi'])->name('admin.notifikasi.test');
    Route::post('/pengaturan-notifikasi/simpan', [NotifikasiController::class, 'simpan'])->name('admin.notifikasi.simpan');
    Route::post('/pengaturan-notifikasi/toggle', [NotifikasiController::class, 'toggle'])->name('admin.notifikasi.toggle');
    Route::get('/pengaturan-notifikasi-alias', [NotifikasiController::class, 'index'])->name('admin.notifikasi');
});

