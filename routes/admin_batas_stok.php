<?php

use App\Http\Controllers\Admin\BatasStokController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/batas-stok', [BatasStokController::class, 'index'])->name('admin.batas-stok');
    Route::match(['post', 'patch'], '/batas-stok/simpan', [BatasStokController::class, 'simpan'])->name('admin.batas-stok.simpan');
});

