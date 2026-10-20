<?php

use App\Http\Controllers\Kasir\InfoBarangKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Info Barang Baru (Frontend Prototype Figma Frame P09)
|--------------------------------------------------------------------------
| CATATAN: Untuk kebutuhan prototype frontend independen, middleware auth
| dan role:KASIR dinonaktifkan sementara. Pulihkan middleware ['auth', 'role:KASIR']
| sebelum integrasi final.
*/

Route::middleware(['web'])->group(function () {
    Route::get('/kasir/info-barang', [InfoBarangKasirController::class, 'index'])->name('kasir.info-barang');
});

