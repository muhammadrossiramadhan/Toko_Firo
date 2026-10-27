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

$backupScheduleFile = storage_path('app/settings/backup_schedule.json');
$backupSchedule = file_exists($backupScheduleFile)
    ? json_decode((string) file_get_contents($backupScheduleFile), true)
    : null;

$waktuBackup = $backupSchedule['waktu'] ?? '22:00';
$tipeBackup = strtolower((string) ($backupSchedule['tipe'] ?? 'harian'));
$aktifBackup = $backupSchedule['aktif'] ?? true;

if ($aktifBackup) {
    $backupEvent = Schedule::command('backup:otomatis')
        ->timezone('Asia/Jakarta')
        ->description('Pencadangan database toko firo otomatis');

    if ($tipeBackup === 'mingguan') {
        $backupEvent->weeklyOn(1, $waktuBackup);
    } elseif ($tipeBackup === 'bulanan') {
        $backupEvent->monthlyOn(1, $waktuBackup);
    } else {
        $backupEvent->dailyAt($waktuBackup);
    }
}

