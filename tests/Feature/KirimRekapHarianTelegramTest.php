<?php

namespace Tests\Feature;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class KirimRekapHarianTelegramTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Jadwal command terdaftar di Laravel Scheduler pada pukul 21:00 WIB.
     */
    public function test_jadwal_rekap_harian_terdaftar_di_scheduler(): void
    {
        /** @var Schedule $schedule */
        $schedule = app(Schedule::class);

        $events = collect($schedule->events())->filter(function (Event $event) {
            return str_contains($event->command, 'telegram:kirim-rekap-harian');
        });

        $this->assertNotEmpty($events, 'Command telegram:kirim-rekap-harian belum terdaftar di scheduler.');

        /** @var Event $rekapEvent */
        $rekapEvent = $events->first();
        $this->assertEquals('Asia/Jakarta', $rekapEvent->timezone);
        $this->assertEquals('0 21 * * *', $rekapEvent->expression);
    }

    /**
     * Perintah artisan berhasil mengeksekusi rekap dan mengirim dokumen PDF ke Telegram.
     */
    public function test_artisan_command_berhasil_mengirim_rekap_ke_telegram(): void
    {
        Config::set('services.telegram.bot_token', '123456:TEST_TOKEN');
        Config::set('services.telegram.admin_chat_id', '11223344');

        DB::table('pengaturan_notifikasi')->updateOrInsert(
            ['kunci' => 'laporan_otomatis'],
            ['nilai' => '1']
        );

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('sendDocument')
            ->atLeast()->once()
            ->withArgs(function ($chatId, $pdfBinary, $filename, $caption) {
                return !empty($chatId) &&
                       str_starts_with($pdfBinary, '%PDF') &&
                       str_starts_with($filename, 'Rekap_Penjualan_') &&
                       str_contains($caption, 'REKAP LAPORAN PENJUALAN HARIAN') &&
                       str_contains($caption, 'Total Omset') &&
                       str_contains($caption, 'Kasir Bertugas');
            })
            ->andReturn(['ok' => true]);

        $this->app->instance(TelegramBotService::class, $mockBot);

        $this->artisan('telegram:kirim-rekap-harian')
            ->expectsOutputToContain('Menyiapkan rekap penjualan harian')
            ->expectsOutputToContain('Rekap laporan penjualan harian berhasil dikirim ke Telegram.')
            ->assertExitCode(0);
    }

    /**
     * Perintah dibatalkan jika toggle notifikasi rekap dinonaktifkan di database.
     */
    public function test_artisan_command_berhenti_jika_toggle_notifikasi_nonaktif(): void
    {
        DB::table('pengaturan_notifikasi')->updateOrInsert(
            ['kunci' => 'laporan_otomatis'],
            ['nilai' => '0']
        );

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldNotReceive('sendDocument');
        $this->app->instance(TelegramBotService::class, $mockBot);

        $this->artisan('telegram:kirim-rekap-harian')
            ->expectsOutputToContain('Pengiriman rekap laporan penjualan harian dinonaktifkan')
            ->assertExitCode(0);
    }

    /**
     * Perintah menangani eksepsi tanpa crash scheduler dan mengembalikan exit code failure.
     */
    public function test_artisan_command_menangani_eksepsi_dengan_aman(): void
    {
        Config::set('services.telegram.bot_token', '123456:TEST_TOKEN');
        Config::set('services.telegram.admin_chat_id', '11223344');

        DB::table('pengaturan_notifikasi')->updateOrInsert(
            ['kunci' => 'laporan_otomatis'],
            ['nilai' => '1']
        );

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('sendDocument')
            ->andThrow(new \RuntimeException('Jaringan terputus ke Telegram'));

        $this->app->instance(TelegramBotService::class, $mockBot);

        $this->artisan('telegram:kirim-rekap-harian')
            ->expectsOutputToContain('Gagal mengirim rekap laporan harian')
            ->assertExitCode(1);
    }
}

