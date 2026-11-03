<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Services\BackupService;
use App\Services\BatasStokService;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramBackupDeliveryTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $admin;
    protected array $filesToCleanup = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        Config::set('services.telegram.bot_token', 'test-bot-token-xyz');
        Config::set('services.telegram.chat_id', '11223344');
        Config::set('services.telegram.admin_chat_id', '55667788');
        Config::set('services.telegram.webhook_secret', 'test-webhook-secret-token');
        Config::set('services.telegram.backup_password', 'SecureTestPass2026!');
    }

    protected function tearDown(): void
    {
        foreach ($this->filesToCleanup as $filePath) {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $retryFile = storage_path('app/settings/backup_retry.json');
        if (file_exists($retryFile)) {
            @unlink($retryFile);
        }

        parent::tearDown();
    }

    /**
     * 1. GET /admin/backup?action=backup tidak menjalankan proses backup atau memutasi state (SEC-13).
     */
    public function test_backup_trigger_rejected_via_http_get_and_renders_cleanly(): void
    {
        $backupCountBefore = DB::table('riwayat_backup')->count();

        $response = $this->actingAs($this->admin)->get('/admin/backup?action=backup');

        $response->assertStatus(200);
        $response->assertSee('Backup Data');

        // Pastikan tidak ada data baru yang tercatat di tabel riwayat_backup
        $backupCountAfter = DB::table('riwayat_backup')->count();
        $this->assertEquals($backupCountBefore, $backupCountAfter);
    }

    /**
     * 2. POST /admin/backup/proses membuat arsip ZIP terenkripsi dan mengirim DUA payload ke Telegram (Teks + Dokumen).
     */
    public function test_backup_trigger_via_post_creates_encrypted_zip_and_sends_dual_payload(): void
    {
        Http::fake([
            'https://api.telegram.org/bot*/sendMessage' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 101],
            ], 200),
            'https://api.telegram.org/bot*/sendDocument' => Http::response([
                'ok' => true,
                'result' => ['message_id' => 102],
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/backup/proses', [
            'tipe' => 'Manual',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $data = $response->json('data');
        $this->assertNotEmpty($data['nama_file']);
        $sqlPath = storage_path('app/backups/' . $data['nama_file']);
        $this->filesToCleanup[] = $sqlPath;

        if (!empty($data['nama_zip'])) {
            $zipPath = storage_path('app/backups/' . $data['nama_zip']);
            $this->filesToCleanup[] = $zipPath;
            $this->assertFileExists($zipPath);
        }

        // Verifikasi DUA payload terkirim ke Telegram: 1 pesan ringkasan teks + 1 lampiran dokumen arsip
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage') &&
                   $request['chat_id'] === '55667788' &&
                   str_contains($request['text'], 'Pemberitahuan Sistem Toko Firo');
        });

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendDocument') &&
                   $request->isMultipart();
        });
    }

    /**
     * 3. Kegagalan API Telegram tidak melaporkan status sukses palsu dan mencatat log retry ke JSON.
     */
    public function test_telegram_api_failure_does_not_report_false_success_and_logs_retry_json(): void
    {
        Http::fake([
            'https://api.telegram.org/bot*/sendMessage' => Http::response([
                'ok' => false,
                'description' => 'Bad Request: chat not found',
            ], 400),
            'https://api.telegram.org/bot*/sendDocument' => Http::response([
                'ok' => false,
                'description' => 'Bad Request: chat not found',
            ], 400),
        ]);

        /** @var BackupService $backupService */
        $backupService = app(BackupService::class);
        $hasil = $backupService->buatBackup('Manual');

        if (!empty($hasil['nama_file'])) {
            $this->filesToCleanup[] = storage_path('app/backups/' . $hasil['nama_file']);
        }
        if (!empty($hasil['nama_zip'])) {
            $this->filesToCleanup[] = storage_path('app/backups/' . $hasil['nama_zip']);
        }

        // Verifikasi pengiriman Telegram tidak dilaporkan sukses
        $this->assertFalse($hasil['telegram_terkirim']);
        $this->assertEquals('Gagal', $hasil['telegram_status']);

        // Verifikasi berkas retry JSON terbentuk
        $retryFile = storage_path('app/settings/backup_retry.json');
        $this->assertFileExists($retryFile);

        $retryData = json_decode((string) file_get_contents($retryFile), true);
        $this->assertIsArray($retryData);
        $this->assertEquals('GAGAL', $retryData['status']);
        $this->assertFalse($retryData['text_terkirim']);
        $this->assertFalse($retryData['document_terkirim']);
    }

    /**
     * 4. Webhook Telegram menolak request dari IP publik liar / subnet tidak resmi (SEC-16).
     */
    public function test_telegram_webhook_rejects_untrusted_ip_subnets(): void
    {
        // IP publik asing tidak tepercaya (misal 185.190.140.1) -> 403 Forbidden
        $responseUntrusted = $this->withServerVariables(['REMOTE_ADDR' => '185.190.140.1'])
            ->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => 'test-webhook-secret-token'])
            ->postJson('/api/telegram/webhook', [
                'update_id' => 9991,
                'message' => [
                    'message_id' => 1,
                    'chat' => ['id' => 55667788],
                    'text' => '/help',
                ],
            ]);

        $responseUntrusted->assertStatus(403);
        $responseUntrusted->assertJson(['error' => 'Forbidden: Unauthorized IP address']);

        // IP resmi dari subnet Telegram (149.154.167.100) -> Diterima
        $responseTrusted = $this->withServerVariables(['REMOTE_ADDR' => '149.154.167.100'])
            ->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => 'test-webhook-secret-token'])
            ->postJson('/api/telegram/webhook', [
                'update_id' => 9992,
                'message' => [
                    'message_id' => 2,
                    'chat' => ['id' => 55667788],
                    'text' => '/help',
                ],
            ]);

        $responseTrusted->assertStatus(200);
    }

    /**
     * 5. Respon bot Telegram untuk perintah dan callback query menggunakan data riil database (tanpa data mockup statis).
     */
    public function test_telegram_webhook_laporan_command_has_no_hardcoded_data(): void
    {
        $uniqueItemCode = 'T-ITM-' . rand(100000, 999999);
        DB::table('daftar_item')->insert([
            'kode_item' => $uniqueItemCode,
            'nama_item' => 'Barang Uji Part4',
            'jenis' => 'ATK',
            'satuan' => 'pcs',
            'harga_pokok' => 1000,
            'harga_jual' => 2500,
            'stok' => 12,
            'batas_minimum' => 5,
            'dibuat_pada' => now(),
        ]);

        $actualItemCount = DB::table('daftar_item')->count();

        $capturedMessages = [];
        $mockBot = \Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('answerCallbackQuery')->andReturn(['ok' => true]);
        $mockBot->shouldReceive('sendMessage')
            ->andReturnUsing(function ($chatId, $text, $keyboard = null) use (&$capturedMessages) {
                $capturedMessages[] = $text;
                return ['ok' => true];
            });

        $this->app->instance(TelegramBotService::class, $mockBot);

        // Uji callback query admin_barang
        $response = $this->withHeaders(['X-Telegram-Bot-Api-Secret-Token' => 'test-webhook-secret-token'])
            ->postJson('/api/telegram/webhook', [
                'update_id' => 8881,
                'callback_query' => [
                    'id' => 'cb-1',
                    'from' => ['id' => 55667788],
                    'data' => 'admin_barang',
                    'message' => [
                        'message_id' => 300,
                        'chat' => ['id' => 55667788],
                    ],
                ],
            ]);

        $response->assertStatus(200);
        $this->assertNotEmpty($capturedMessages);

        $lastMessage = end($capturedMessages);
        // Pastikan tidak ada teks hardcoded "47 item" jika total barang bukan 47
        $this->assertStringContainsString("Total Master Barang: <b>{$actualItemCount} item</b>", $lastMessage);
    }

    /**
     * 6. Peringatan stok minimum via BatasStokService dieksekusi setelah database transaction selesai di-commit (afterCommit).
     */
    public function test_stock_warning_executes_outside_transaction_after_commit(): void
    {
        $kodeItem = 'RB-' . rand(100000, 999999);
        DB::table('daftar_item')->insert([
            'kode_item' => $kodeItem,
            'nama_item' => 'Barang Rollback Uji',
            'jenis' => 'ATK',
            'satuan' => 'pcs',
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'stok' => 2,
            'batas_minimum' => 5,
            'dibuat_pada' => now(),
        ]);

        $messagesSent = 0;
        $mockBot = \Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('sendMessage')
            ->andReturnUsing(function () use (&$messagesSent) {
                $messagesSent++;
                return ['ok' => true];
            });
        $this->app->instance(TelegramBotService::class, $mockBot);

        /** @var BatasStokService $batasService */
        $batasService = app(BatasStokService::class);

        // Skenario A: Transaksi yang di-rollback TIDAK BOLEH memicu pengiriman pesan Telegram
        try {
            DB::transaction(function () use ($batasService, $kodeItem) {
                $batasService->updateBatasItem($kodeItem, 8, $this->admin->id_pengguna);
                throw new \Exception('Simulasi kegagalan transaksi DB!');
            });
        } catch (\Throwable $e) {
            // Ditangkap sengaja
        }

        $this->assertEquals(0, $messagesSent, 'Pesan Telegram tidak boleh terkirim jika DB transaction rollback.');

        // Skenario B: Transaksi yang commit berhasil mengirim peringatan setelah commit
        DB::transaction(function () use ($batasService, $kodeItem) {
            $batasService->updateBatasItem($kodeItem, 9, $this->admin->id_pengguna);
        });

        $this->assertEquals(1, $messagesSent, 'Pesan Telegram wajib terkirim setelah DB transaction berhasil commit.');
    }

    /**
     * 7. Pembuatan arsip backup tanpa kata sandi terkonfigurasi melempar RuntimeException secara aman (SEC-08).
     */
    public function test_backup_archive_without_password_throws_safe_runtime_exception(): void
    {
        Config::set('services.telegram.backup_password', null);
        putenv('BACKUP_ENCRYPTION_PASSWORD=');
        putenv('BACKUP_ARCHIVE_PASSWORD=');
        unset($_ENV['BACKUP_ENCRYPTION_PASSWORD'], $_ENV['BACKUP_ARCHIVE_PASSWORD']);

        $tempDir = storage_path('app/temp_test_pass');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $sqlFile = $tempDir . '/empty-pass.sql';
        $zipFile = $tempDir . '/empty-pass.zip';
        file_put_contents($sqlFile, '-- Sample SQL');

        $this->filesToCleanup[] = $sqlFile;
        $this->filesToCleanup[] = $zipFile;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Kata sandi enkripsi backup (BACKUP_ENCRYPTION_PASSWORD) belum dikonfigurasi.');

        /** @var BackupService $backupService */
        $backupService = app(BackupService::class);
        $backupService->createEncryptedZip($sqlFile, $zipFile, '');
    }
}
