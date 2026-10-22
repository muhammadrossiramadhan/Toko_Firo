<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\DemoBarangController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\OtpController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');

// Redirect alias /login ke /masuk
Route::redirect('/login', '/masuk');

// Autentikasi 2FA (Step 1: Masuk)
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/masuk', [LoginController::class, 'authenticate']);
});

// Pending 2FA (Step 2: Verifikasi OTP)
Route::middleware('pending2fa')->group(function () {
    Route::get('/verifikasi', [OtpController::class, 'showVerifyForm'])->name('verifikasi');
    Route::post('/verifikasi', [OtpController::class, 'verify']);
    Route::post('/verifikasi/resend', [OtpController::class, 'resend'])->name('verifikasi.resend');
});

// Authenticated
Route::post('/logout', LogoutController::class)->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/demo/barang', [DemoBarangController::class, 'index'])->name('demo.barang');
});
