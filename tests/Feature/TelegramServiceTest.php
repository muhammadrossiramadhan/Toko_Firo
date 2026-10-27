<?php

namespace Tests\Feature;

use App\Services\BackupService;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramReportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use ZipArchive;

class TelegramServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.telegram.bot_token', 'test-dummy-bot-token-123');
        Config::set('services.telegram.chat_id', '12345678');
        Config::set('services.telegram.admin_chat_id', '87654321');
        Config::set('services.telegram.backup_password', 'TestPasswordAES256!');
    }

    /**
     * Uji pengiriman pesan teks via TelegramBotService dengan Http::fake().
     */
    public function test_telegram_bot_service_mengirim_pesan_teks(): void
    {
        Http::fake([
            'https://api.telegram.org/bot*/sendMessage' => Http::response([
                'ok' => true,
                'result' => [
                    'message_id' => 999,
                    'text' => 'Pesan uji coba',
                ],
            ], 200),
        ]);

        $bot = new TelegramBotService('test-dummy-bot-token-123');
        $hasil = $bot->sendMessage('12345678', 'Halo Toko Firo');

        $this->assertIsArray($hasil);
        $this->assertTrue($hasil['ok']);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage') &&
                   $request['chat_id'] === '12345678' &&
                   $request['text'] === 'Halo Toko Firo';
        });
    }

    /**
     * Uji pengiriman dokumen biner via TelegramBotService dengan Http::fake().
     */
    public function test_telegram_bot_service_mengirim_dokumen_biner(): void
    {
        Http::fake([
            'https://api.telegram.org/bot*/sendDocument' => Http::response([
                'ok' => true,
                'result' => [
                    'message_id' => 1000,
                    'document' => ['file_name' => 'laporan.pdf'],
                ],
            ], 200),
        ]);

        $bot = new TelegramBotService('test-dummy-bot-token-123');
        $fakePdfContent = "%PDF-1.4 Fake PDF Content";
        $hasil = $bot->sendDocument('12345678', $fakePdfContent, 'laporan.pdf', 'Caption Test');

        $this->assertIsArray($hasil);
        $this->assertTrue($hasil['ok']);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendDocument') &&
                   $request->isMultipart();
        });
    }

    /**
     * Uji pembentukan binary PDF pada TelegramReportService (Sales PDF & Restock PDF).
     */
    public function test_telegram_report_service_menghasilkan_binary_pdf(): void
    {
        $reportService = app(TelegramReportService::class);

        // 1. Generate Sales PDF
        $salesPdf = $reportService->generateSalesPdfBinary([
            'tanggal' => 'Senin, 26 Oktober 2026',
            'total_penjualan' => 1500000,
            'jumlah_transaksi' => 5,
            'barang_terjual' => 20,
            'produk_terlaris' => 'Buku Tulis Sidu 38L',
            'transaksi' => [],
        ]);

        $this->assertNotEmpty($salesPdf);
        $this->assertStringStartsWith('%PDF-', $salesPdf);

        // 2. Generate Restock PDF
        $restockPdf = $reportService->generateRestockPdfBinary();
        $this->assertNotEmpty($restockPdf);
        $this->assertStringStartsWith('%PDF-', $restockPdf);

        // 3. Verifikasi Sales Caption
        $caption = $reportService->buildSalesCaption([
            'tanggal' => '26/10/2026',
            'total_penjualan' => 250000,
            'jumlah_transaksi' => 2,
            'barang_terjual' => 10,
            'produk_terlaris' => 'Pulpen Standard',
        ]);
        $this->assertStringContainsString('LAPORAN PENJUALAN HARIAN', $caption);
        $this->assertStringContainsString('Rp 250.000', $caption);
    }

    /**
     * Uji pembungkusan arsip ZIP terenkripsi AES-256 pada BackupService.
     */
    public function test_backup_service_membuat_arsip_zip_terenkripsi_aes256(): void
    {
        $backupService = app(BackupService::class);

        $tempDir = storage_path('app/temp_test_backup');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $dummySqlPath = $tempDir . '/test-dump.sql';
        $dummyZipPath = $tempDir . '/test-backup.zip';
        $password = 'SecretKeyAES256Test';

        file_put_contents($dummySqlPath, '-- Test SQL Content for Toko Firo Dump');

        // Buat zip terenkripsi
        $sukses = $backupService->createEncryptedZip($dummySqlPath, $dummyZipPath, $password);

        $this->assertTrue($sukses);
        $this->assertFileExists($dummyZipPath);

        // Verifikasi bahwa arsip ZIP valid
        $zip = new ZipArchive();
        $res = $zip->open($dummyZipPath);
        $this->assertTrue($res === true);
        $this->assertEquals(1, $zip->numFiles);
        $this->assertEquals('test-dump.sql', $zip->getNameIndex(0));
        $zip->close();

        // Verifikasi ekstraksi tanpa password
        $zipTanpaPass = new ZipArchive();
        $zipTanpaPass->open($dummyZipPath);
        $kontenTanpaPass = $zipTanpaPass->getFromIndex(0);
        $this->assertTrue($kontenTanpaPass === false || $kontenTanpaPass !== '-- Test SQL Content for Toko Firo Dump');
        $zipTanpaPass->close();

        // Verifikasi ekstraksi dengan password benar berhasil
        $zipDenganPass = new ZipArchive();
        $zipDenganPass->open($dummyZipPath);
        $zipDenganPass->setPassword($password);
        $kontenDenganPass = $zipDenganPass->getFromIndex(0);
        $this->assertEquals('-- Test SQL Content for Toko Firo Dump', $kontenDenganPass);
        $zipDenganPass->close();

        unset($zip, $zipTanpaPass, $zipDenganPass);

        // Bersihkan file uji
        if (file_exists($dummySqlPath)) @unlink($dummySqlPath);
        if (file_exists($dummyZipPath)) @unlink($dummyZipPath);
        if (is_dir($tempDir)) @rmdir($tempDir);
    }
}
