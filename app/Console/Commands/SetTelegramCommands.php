<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Command;

class SetTelegramCommands extends Command
{
    /**
     * Nama dan signature dari perintah console.
     *
     * @var string
     */
    protected $signature = 'telegram:set-commands';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Mendaftarkan command bot Telegram ke server Telegram API';

    /**
     * Eksekusi perintah console.
     */
    public function handle(TelegramBotService $botService): int
    {
        $this->info('Mendaftarkan daftar perintah bot ke Telegram API...');

        $commands = [
            [
                'command' => 'start',
                'description' => 'Mulai bot dan tampilkan menu utama Toko Firo',
            ],
            [
                'command' => 'menu',
                'description' => 'Buka menu utama navigasi bot',
            ],
            [
                'command' => 'menu_admin',
                'description' => 'Buka menu navigasi khusus Administrator',
            ],
            [
                'command' => 'menu_kasir',
                'description' => 'Buka menu navigasi khusus Kasir',
            ],
        ];

        $response = $botService->setMyCommands($commands);

        if ($response && ($response['ok'] ?? false) === true) {
            $this->info('Berhasil! Perintah bot Telegram telah didaftarkan:');
            foreach ($commands as $cmd) {
                $this->line(" - /{$cmd['command']} : {$cmd['description']}");
            }
            return self::SUCCESS;
        }

        $errorMsg = $response['description'] ?? 'Gagal menghubungi Telegram API atau token belum terkonfigurasi.';
        $this->error("Gagal mendaftarkan perintah bot: {$errorMsg}");

        return self::FAILURE;
    }
}

