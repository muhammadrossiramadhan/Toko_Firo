<?php

use App\Http\Controllers\Kasir\TransaksiKasirController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Modul Kasir: Transaksi Penjualan (Frontend Prototype Figma Frame P06)
|--------------------------------------------------------------------------
| CATATAN: Untuk kebutuhan prototype frontend independen, middleware auth
| dan role:KASIR dinonaktifkan sementara. Pulihkan middleware ['auth', 'role:KASIR']
| sebelum integrasi produksi.
*/

Route::middleware(['web'])->group(function () {
    Route::get('/kasir/transaksi', [TransaksiKasirController::class, 'index'])->name('kasir.transaksi');
});

