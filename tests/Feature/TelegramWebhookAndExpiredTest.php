<?php

namespace Tests\Feature;

use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramReportService;
use Tests\TestCase;

class TelegramWebhookAndExpiredTest extends TestCase
{
    /**
     * Halaman sesi berakhir memuat seluruh elemen K12 & K18.
     */
    public function test_halaman_sesi_berakhir_memuat_konten_dan_patuh_k12_k18(): void
    {
        $response = $this->get('/sesi-berakhir');

        $response->assertStatus(200);
        $response->assertSee('Toko Firo');
        $response->assertSee('Sesi Telah Berakhir');
        $response->assertSee('Waktu Masuk Kedaluwarsa');
        $response->assertSee('Masuk Kembali');
        $response->assertSee('Kembali ke Beranda');
        $response->assertSee(route('login'));
        $response->assertSee('--skala', false);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Halaman error 419 memuat tampilan expired.
     */
    public function test_halaman_error_419_memuat_tampilan_expired(): void
    {
        $response = $this->view('errors.419');

        $response->assertSee('Waktu Masuk Kedaluwarsa');
        $response->assertSee('Sesi Telah Berakhir');
        $response->assertDontSee('<svg', false);
    }

    /**
     * Webhook Telegram dapat menerima request POST tanpa terblokir CSRF.
     */
    public function test_telegram_webhook_menerima_post_tanpa_csrf(): void
    {
        $response = $this->postJson('/api/telegram/webhook', [
            'message' => [
                'chat' => ['id' => 12345678],
                'text' => '/menu',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok', 'type' => 'message']);
    }

    /**
     * Webhook Telegram memproses callback query tombol inline.
     */
    public function test_telegram_webhook_menerima_callback_query(): void
    {
        $response = $this->postJson('/api/telegram/webhook', [
            'callback_query' => [
                'id' => 'cb_123',
                'message' => [
                    'chat' => ['id' => 12345678],
                ],
                'data' => 'menu_admin',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok', 'type' => 'callback_query']);
    }

    /**
     * Service pembuatan PDF laporan penjualan menghasilkan binary dokumen PDF yang valid.
     */
    public function test_telegram_report_service_menghasilkan_pdf_valid(): void
    {
        $service = app(TelegramReportService::class);
        $summary = $service->getSummaryData();
        $this->assertNotEmpty($summary['total_penjualan']);
        $this->assertNotEmpty($summary['saldo_kasir']);

        $pdfBinary = $service->generateSalesPdfBinary($summary);
        $this->assertStringStartsWith('%PDF', $pdfBinary);

        $caption = $service->buildSalesCaption($summary);
        $this->assertStringContainsString('LAPORAN PENJUALAN HARIAN', $caption);
        $this->assertStringContainsString('Total Penjualan', $caption);
    }
}

