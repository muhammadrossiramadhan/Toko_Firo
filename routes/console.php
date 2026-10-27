<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('telegram:kirim-rekap-harian')
    ->dailyAt('21:00')
    ->timezone('Asia/Jakarta')
    ->description('Kirim rekap laporan penjualan harian otomatis ke Telegram');

