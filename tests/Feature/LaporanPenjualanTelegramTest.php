<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramReportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class LaporanPenjualanTelegramTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Endpoint kirim telegram mengembalikan error 500 jika kredensial bot belum terkonfigurasi.
     */
    public function test_kirim_telegram_gagal_jika_kredensial_belum_diatur(): void
    {
        Config::set('services.telegram.bot_token', null);
        Config::set('services.telegram.admin_chat_id', null);
        Config::set('services.telegram.chat_id', null);

        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.laporan.kirim-telegram'), [
            'mulai' => date('Y-m-d'),
            'sampai' => date('Y-m-d'),
        ]);

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
        ]);
        $response->assertJsonPath('status', 'error');
    }

    /**
     * Endpoint kirim telegram berhasil mengirim laporan PDF dan pesan rekap ke Telegram saat kredensial valid.
     */
    public function test_kirim_telegram_berhasil_mengirim_laporan(): void
    {
        Config::set('services.telegram.bot_token', '123456789:TEST_MOCK_TOKEN');
        Config::set('services.telegram.admin_chat_id', '987654321');

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('sendDocument')
            ->once()
            ->andReturn(['ok' => true]);

        $this->app->instance(TelegramBotService::class, $mockBot);

        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.laporan.kirim-telegram'), [
            'mulai' => date('Y-m-d'),
            'sampai' => date('Y-m-d'),
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Laporan penjualan berhasil dikirim ke Telegram!',
        ]);
    }

    /**
     * Endpoint kirim telegram menangani eksepsi tak terduga dengan graceful JSON 500.
     */
    public function test_kirim_telegram_menangani_eksepsi_dengan_status_500(): void
    {
        Config::set('services.telegram.bot_token', '123456789:TEST_MOCK_TOKEN');
        Config::set('services.telegram.admin_chat_id', '987654321');

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('sendDocument')
            ->once()
            ->andThrow(new \RuntimeException('Koneksi Telegram terputus'));

        $this->app->instance(TelegramBotService::class, $mockBot);

        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.laporan.kirim-telegram'), [
            'mulai' => date('Y-m-d'),
            'sampai' => date('Y-m-d'),
        ]);

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
        ]);
    }

    /**
     * Tombol kirim ke telegram aktif di halaman laporan penjualan dan tidak lagi disabled.
     */
    public function test_tombol_kirim_telegram_aktif_di_halaman_laporan(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan');

        $response->assertStatus(200);
        $response->assertSee('Kirim ke Telegram');
        $response->assertSee('btn-kirim-telegram');
        $response->assertSee('kirimLaporanTelegram(this)');
        $response->assertDontSee('bg-gray-200 text-gray-400 font-medium rounded-xl text-base cursor-not-allowed', false);
    }
}
