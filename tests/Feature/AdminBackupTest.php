<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class AdminBackupTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        foreach ($this->filesToCleanup as $filePath) {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        Mockery::close();
        parent::tearDown();
    }

    public function test_halaman_backup_dapat_diakses_oleh_admin(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/backup');

        $response->assertStatus(200);
        $response->assertSee('Backup Data');
        $response->assertSee('Backup Sekarang');
        $response->assertDontSee('<svg', false);
    }

    public function test_pembuatan_backup_via_endpoint_post(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/backup/buat', [
            'tipe' => 'Manual',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $namaFile = $response->json('data.nama_file');
        $this->assertNotEmpty($namaFile);

        $filePath = storage_path('app/backups/' . $namaFile);
        $this->filesToCleanup[] = $filePath;

        $this->assertFileExists($filePath);
        $this->assertGreaterThan(0, filesize($filePath));
    }

    public function test_unduh_backup_berhasil_dan_cegah_path_traversal(): void
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $dummyName = 'test_download_' . time() . '.sql';
        $dummyPath = $dir . DIRECTORY_SEPARATOR . $dummyName;
        file_put_contents($dummyPath, '-- Test dump file content');
        $this->filesToCleanup[] = $dummyPath;

        // Unduh berkas yang valid
        $responseValid = $this->actingAs($this->admin)->get("/admin/backup/unduh/{$dummyName}");
        $responseValid->assertStatus(200);
        $responseValid->assertHeader('content-disposition');

        // Uji coba path traversal
        $responseMalicious = $this->actingAs($this->admin)->get('/admin/backup/unduh/..%2F..%2Ftest.sql');
        $this->assertContains($responseMalicious->status(), [400, 404]);
    }

    public function test_hapus_backup_berhasil(): void
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $dummyName = 'test_delete_' . time() . '.sql';
        $dummyPath = $dir . DIRECTORY_SEPARATOR . $dummyName;
        file_put_contents($dummyPath, '-- Temporary file to delete');

        $response = $this->actingAs($this->admin)->deleteJson("/admin/backup/{$dummyName}");
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        $this->assertFileDoesNotExist($dummyPath);
    }

    public function test_notifikasi_telegram_dikirim_saat_backup_selesai(): void
    {
        Config::set('services.telegram.admin_chat_id', '123456789');

        DB::table('pengaturan_notifikasi')->updateOrInsert(
            ['kunci' => 'notif_backup'],
            ['nilai' => '1', 'diubah_pada' => now()]
        );

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('kirimPesanTeks')
            ->once()
            ->withArgs(function ($chatId, $pesan) {
                return $chatId === '123456789'
                    && str_contains($pesan, 'Pemberitahuan Sistem Toko Firo')
                    && str_contains($pesan, 'Backup database berhasil dibuat!')
                    && str_contains($pesan, 'Status: Aman tersimpan.');
            })
            ->andReturn(true);

        $this->app->instance(TelegramBotService::class, $mockBot);

        $response = $this->actingAs($this->admin)->postJson('/admin/backup/buat', [
            'tipe' => 'Manual',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $namaFile = $response->json('data.nama_file');
        if (!empty($namaFile)) {
            $this->filesToCleanup[] = storage_path('app/backups/' . $namaFile);
        }
    }
}
