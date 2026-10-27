<?php

use App\Http\Controllers\Kasir\StokKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Lihat Stok Barang
|--------------------------------------------------------------------------
| Izin akses diberikan untuk peran kasir, admin, dan pemilik toko.
*/

Route::middleware(['web', 'auth', 'role:kasir,admin,pemilik'])->group(function () {
    Route::get('/kasir/stok', [StokKasirController::class, 'index'])->name('kasir.stok');
    Route::get('/kasir/stok/cari', [StokKasirController::class, 'cariAjax'])->name('kasir.stok.cari');
});
