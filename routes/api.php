<?php

use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rute API untuk integrasi webhook dan layanan eksternal Toko Firo.
|
*/

Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])->middleware('throttle:30,1')->name('telegram.webhook');

