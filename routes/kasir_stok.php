<?php

use App\Http\Controllers\Kasir\StokKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Lihat Stok Barang (Frontend Prototype Figma Frame P07)
|--------------------------------------------------------------------------
| CATATAN: Untuk kebutuhan prototype frontend independen, middleware auth
| dan role:KASIR dinonaktifkan sementara. Pulihkan middleware ['auth', 'role:KASIR']
| sebelum integrasi final.
*/

Route::middleware(['web'])->group(function () {
    Route::get('/kasir/stok', [StokKasirController::class, 'index'])->name('kasir.stok');
});

