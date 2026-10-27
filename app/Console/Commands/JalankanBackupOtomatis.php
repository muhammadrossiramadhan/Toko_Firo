<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class JalankanBackupOtomatis extends Command
{
    /**
     * Nama dan signature dari perintah console.
     *
     * @var string
     */
    protected $signature = 'backup:otomatis {--force : Jalankan backup meskipun jadwal otomatis dinonaktifkan}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Jalankan proses pencadangan database Toko Firo otomatis dan kirim notifikasi/file ke Telegram';

    /**
     * Eksekusi perintah console.
     */
    public function handle(BackupService $backupService): int
    {
        $this->info('Memulai pengecekan tugas backup database otomatis Toko Firo...');

        $jadwal = $backupService->getJadwalBackup();
        $isForce = (bool) $this->option('force');

        if (!$isForce && empty($jadwal['aktif'])) {
            $pesan = 'Pencadangan database otomatis dilewati: Status jadwal sedang dinonaktifkan.';
            $this->warn($pesan);
            Log::info($pesan);
            return self::SUCCESS;
        }

        try {
            $this->info('Menjalankan pencadangan database...');
            $hasil = $backupService->buatBackup('Otomatis');

            $pesanSukses = "Cadangan database otomatis berhasil dibuat: {$hasil['nama_file']} ({$hasil['ukuran']}).";
            $this->info($pesanSukses);
            Log::info($pesanSukses);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $pesanGagal = 'Gagal menjalankan pencadangan database otomatis: ' . $e->getMessage();
            $this->error($pesanGagal);
            Log::error($pesanGagal, [
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}

