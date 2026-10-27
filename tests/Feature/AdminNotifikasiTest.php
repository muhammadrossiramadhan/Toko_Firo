<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class AdminNotifikasiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Admin aktif dapat mengakses halaman pengaturan notifikasi dan melihat konfigurasi bot.
     */
    public function test_halaman_pengaturan_notifikasi_dapat_diakses_oleh_admin(): void
    {
        Config::set('services.telegram.bot_token', '123456789:ABCdefGHIjklMNOpqrsTUVwxyz');
        Config::set('services.telegram.admin_chat_id', '987654321');

        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.notifikasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Notifikasi Bot');
        $response->assertSee('Telegram Bot');
        $response->assertSee('Terhubung');
        $response->assertSee('Kirim Pesan Tes');
        $response->assertSee('btn-test-koneksi');
        $response->assertSee('toggle-rekap_harian');
        $response->assertDontSee('<svg', false);
    }

    /**
     * Uji coba pesan Telegram berhasil mengirim pesan teks dan merespons status 200 JSON.
     */
    public function test_uji_coba_pesan_telegram_berhasil(): void
    {
        Config::set('services.telegram.bot_token', '123456789:ABCdefGHIjklMNOpqrsTUVwxyz');
        Config::set('services.telegram.admin_chat_id', '987654321');

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('kirimPesanTeks')
            ->once()
            ->withArgs(function ($chatId, $pesan) {
                return $chatId === '987654321' &&
                       str_contains($pesan, 'Tes Koneksi Toko Firo: Bot Telegram berhasil terhubung ke sistem.');
            })
            ->andReturn(['ok' => true]);

        $this->app->instance(TelegramBotService::class, $mockBot);

        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.notifikasi.test'));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Pesan uji coba berhasil dikirim ke Telegram!',
        ]);
    }

    /**
     * Uji coba pesan Telegram gagal dan mengembalikan status 500 JSON jika token/chat ID belum diatur.
     */
    public function test_uji_coba_pesan_telegram_gagal_jika_kredensial_kosong(): void
    {
        Config::set('services.telegram.bot_token', null);
        Config::set('services.telegram.admin_chat_id', null);
        Config::set('services.telegram.chat_id', null);

        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.notifikasi.test'));

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
        ]);
    }

    /**
     * Uji coba pesan Telegram menangani error exception dengan aman dan mengembalikan respons HTTP 500 JSON.
     */
    public function test_uji_coba_pesan_telegram_menangani_eksepsi(): void
    {
        Config::set('services.telegram.bot_token', '123456789:ABCdefGHIjklMNOpqrsTUVwxyz');
        Config::set('services.telegram.admin_chat_id', '987654321');

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('kirimPesanTeks')
            ->once()
            ->andThrow(new \RuntimeException('Connection timed out'));

        $this->app->instance(TelegramBotService::class, $mockBot);

        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.notifikasi.test'));

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'error',
        ]);
    }

    /**
     * Menyimpan toggle pengaturan notifikasi berhasil memperbarui persistent storage.
     */
    public function test_simpan_toggle_pengaturan_notifikasi_berhasil(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.notifikasi.simpan'), [
            'id' => 'rekap_harian',
            'aktif' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Pengaturan notifikasi berhasil disimpan.',
        ]);

        $nilaiDb = DB::table('pengaturan_notifikasi')
            ->where('kunci', 'laporan_otomatis')
            ->value('nilai');

        $this->assertEquals('0', $nilaiDb);
    }

    /**
     * Menyimpan toggle dengan ID yang tidak valid mengembalikan respons 422 JSON.
     */
    public function test_simpan_toggle_pengaturan_notifikasi_menolak_id_tidak_valid(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.notifikasi.simpan'), [
            'id' => 'invalid_toggle_id',
            'aktif' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => 'ID pengaturan notifikasi tidak valid.',
        ]);
    }
}

