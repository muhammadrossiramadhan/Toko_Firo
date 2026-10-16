<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DemoBarangController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// DEMO: wajib diberi middleware auth dan role:ADMIN sebelum merge ke branch bersama.
Route::get('/demo/barang', [DemoBarangController::class, 'index'])->name('demo.barang');

