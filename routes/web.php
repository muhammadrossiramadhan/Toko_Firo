<?php

use App\Http\Controllers\DemoBarangController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// DEMO: wajib diberi middleware auth dan role:ADMIN sebelum merge ke branch bersama.
Route::get('/demo/barang', [DemoBarangController::class, 'index'])->name('demo.barang');

