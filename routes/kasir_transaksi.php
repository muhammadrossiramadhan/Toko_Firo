<?php

use App\Http\Controllers\Kasir\TransaksiKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Transaksi Penjualan
|--------------------------------------------------------------------------
| Izin akses diberikan untuk peran kasir, admin, dan pemilik toko.
*/

Route::middleware(['web', 'auth', 'role:kasir,admin,pemilik'])->group(function () {
    Route::get('/kasir/transaksi', [TransaksiKasirController::class, 'index'])->name('kasir.transaksi');
    Route::post('/kasir/transaksi/keranjang', [TransaksiKasirController::class, 'updateKeranjang'])->name('kasir.transaksi.keranjang');
    Route::post('/kasir/transaksi/simpan', [TransaksiKasirController::class, 'simpan'])->name('kasir.transaksi.simpan');
    Route::post('/kasir/transaksi/bayar', [TransaksiKasirController::class, 'simpan'])->name('kasir.transaksi.bayar');
    Route::get('/kasir/transaksi/{nota}/detail', [TransaksiKasirController::class, 'detail'])->name('kasir.transaksi.detail');
    Route::get('/kasir/transaksi/{nota}/cetak', [TransaksiKasirController::class, 'cetakStruk'])->name('kasir.transaksi.cetak');
});
