<?php

use App\Http\Controllers\Kasir\StokKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Lihat Stok Barang
|--------------------------------------------------------------------------
| Izin akses diberikan untuk peran kasir, admin, dan pemilik toko.
*/

Route::get('/kasir/stok', [StokKasirController::class, 'index'])->name('kasir.stok');
Route::get('/kasir/stok/cari', [StokKasirController::class, 'cariAjax'])->name('kasir.stok.cari');
Route::get('/kasir/stok-index', [StokKasirController::class, 'index'])->name('kasir.stok.index');
