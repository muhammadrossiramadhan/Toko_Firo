<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\DemoBarangController;
use App\Http\Controllers\Kasir\TransaksiKasirController;
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

// Guest only (step 1)
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/masuk', [LoginController::class, 'authenticate'])->middleware('throttle:5,1');
});

// Pending 2FA (step 2)
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

// DEMO: publik sebelum merge final
Route::get('/demo/barang', [DemoBarangController::class, 'index'])->name('demo.barang');

// Grup Rute Kasir: Transaksi Penjualan
Route::middleware(['auth', 'role:kasir,KASIR'])->group(function () {
    Route::get('/kasir/transaksi', [TransaksiKasirController::class, 'index'])->name('kasir.transaksi.index');
    Route::post('/kasir/transaksi/simpan', [TransaksiKasirController::class, 'simpan'])->name('kasir.transaksi.simpan');
    Route::get('/kasir/transaksi/{nota}/detail', [TransaksiKasirController::class, 'detail'])->name('kasir.transaksi.detail');
    Route::get('/kasir/transaksi/{nota}/cetak', [TransaksiKasirController::class, 'cetakStruk'])->name('kasir.transaksi.cetak');
});

// Resolusi alias nama rute jika kasir_transaksi.php menimpa nama rute kasir.transaksi.index
\Illuminate\Support\Facades\URL::resolveMissingNamedRoutesUsing(function ($name, $parameters, $absolute) {
    if ($name === 'kasir.transaksi.index') {
        return route('kasir.transaksi', $parameters, $absolute);
    }
    if ($name === 'kasir.transaksi') {
        return route('kasir.transaksi.index', $parameters, $absolute);
    }
    return null;
});
