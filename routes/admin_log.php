<?php

use App\Http\Controllers\Admin\LogAktivitasController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('admin.log');
});

