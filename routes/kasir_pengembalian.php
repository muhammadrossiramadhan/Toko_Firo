<?php

use App\Http\Controllers\Kasir\PengembalianKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Pengembalian Barang (Retur Penjualan)
|--------------------------------------------------------------------------
| Izin akses diberikan untuk peran kasir, admin, dan pemilik toko.
*/

Route::get('/kasir/pengembalian', [PengembalianKasirController::class, 'index'])->name('kasir.pengembalian');
Route::get('/kasir/pengembalian/cari', [PengembalianKasirController::class, 'cariTransaksi'])->name('kasir.pengembalian.cari');
Route::get('/kasir/pengembalian/{nota}/detail', [PengembalianKasirController::class, 'detailTransaksi'])->name('kasir.pengembalian.detail');
Route::post('/kasir/pengembalian/proses', [PengembalianKasirController::class, 'proses'])->name('kasir.pengembalian.proses');

