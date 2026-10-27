<?php

use App\Http\Controllers\Kasir\InfoBarangKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Info Barang Baru
|--------------------------------------------------------------------------
| Izin akses diberikan untuk peran kasir, admin, dan pemilik toko.
*/

Route::middleware(['web', 'auth', 'role:kasir,admin,pemilik'])->group(function () {
    Route::get('/kasir/info-barang', [InfoBarangKasirController::class, 'index'])->name('kasir.info-barang');
});
