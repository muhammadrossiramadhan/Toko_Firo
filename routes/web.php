<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\DemoBarangController;
use App\Http\Controllers\KatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');
Route::get('/masuk', function () { return view('masuk'); })->name('masuk');
Route::get('/verifikasi', function () { return view('verifikasi'); })->name('verifikasi');

// DEMO: wajib diberi middleware auth dan role:ADMIN sebelum merge ke branch bersama.
Route::get('/demo/barang', [DemoBarangController::class, 'index'])->name('demo.barang');
