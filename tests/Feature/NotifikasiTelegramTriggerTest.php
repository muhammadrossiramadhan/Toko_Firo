<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Services\BackupService;
use App\Services\Kasir\TransaksiKasirService;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class NotifikasiTelegramTriggerTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $admin;
    protected Pengguna $kasir;
    protected array $filesToCleanup = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $this->kasir = Pengguna::factory()->create([
            'role' => 'KASIR',
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

    /**
     * Uji coba proses backup database memicu pengiriman pesan notifikasi ke Telegram bot.
     */
    public function test_backup_database_memicu_pengiriman_pesan_telegram(): void
    {
        Config::set('services.telegram.admin_chat_id', '777888999');

        DB::table('pengaturan_notifikasi')->updateOrInsert(
            ['kunci' => 'backup_selesai'],
            ['nilai' => '1', 'diubah_pada' => now()]
        );

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('kirimPesanTeks')
            ->once()
            ->withArgs(function ($chatId, $pesan) {
                return $chatId === '777888999'
                    && str_contains($pesan, "Pemberitahuan Sistem Toko Firo")
                    && str_contains($pesan, "Backup database berhasil dibuat!")
                    && str_contains($pesan, "Status: Aman tersimpan.");
            })
            ->andReturn(true);

        $this->app->instance(TelegramBotService::class, $mockBot);

        /** @var BackupService $backupService */
        $backupService = app(BackupService::class);
        $hasil = $backupService->buatBackup('Manual');

        $this->assertNotEmpty($hasil['nama_file']);
        $filePath = storage_path('app/backups/' . $hasil['nama_file']);
        $this->filesToCleanup[] = $filePath;
        $this->assertFileExists($filePath);
    }

    /**
     * Uji coba transaksi kasir yang membuat stok barang mencapai batas minimum memicu peringatan Telegram.
     */
    public function test_transaksi_kasir_memicu_peringatan_telegram_saat_stok_menyentuh_batas_minimum(): void
    {
        Config::set('services.telegram.admin_chat_id', '777888999');

        DB::table('pengaturan_notifikasi')->updateOrInsert(
            ['kunci' => 'stok_minimum'],
            ['nilai' => '1', 'diubah_pada' => now()]
        );

        $kodeBarang = 'BRG-TST-' . time();
        DB::table('daftar_item')->insert([
            'kode_item' => $kodeBarang,
            'nama_item' => 'Buku Tulis Sidu 38Lbr',
            'jenis' => 'Alat Tulis',
            'stok' => 5,
            'batas_minimum' => 3,
            'harga_pokok' => 3000,
            'harga_jual' => 4500,
            'satuan' => 'pcs',
        ]);

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('kirimPesanTeks')
            ->once()
            ->withArgs(function ($chatId, $pesan) use ($kodeBarang) {
                return $chatId === '777888999'
                    && str_contains($pesan, 'PERINGATAN STOK MINIMUM')
                    && str_contains($pesan, $kodeBarang)
                    && str_contains($pesan, 'Sisa Stok: 3 pcs')
                    && str_contains($pesan, 'Batas Minimum: 3')
                    && str_contains($pesan, 'Harap segera lakukan pengadaan/restock ulang barang ini!');
            })
            ->andReturn(true);

        $this->app->instance(TelegramBotService::class, $mockBot);

        /** @var TransaksiKasirService $transaksiService */
        $transaksiService = app(TransaksiKasirService::class);
        $transaksi = $transaksiService->prosesTransaksi([
            'items' => [
                [
                    'kode_item' => $kodeBarang,
                    'qty' => 2,
                ],
            ],
            'nominal_bayar' => 10000,
            'metode_bayar' => 'TUNAI',
        ], $this->kasir->id_pengguna);

        $this->assertNotNull($transaksi);

        // Verifikasi stok riil di database berkurang menjadi 3
        $stokTerkini = (int) DB::table('daftar_item')->where('kode_item', $kodeBarang)->value('stok');
        $this->assertEquals(3, $stokTerkini);
    }

    /**
     * Transaksi kasir tidak mengirim peringatan jika stok sisa masih di atas batas minimum.
     */
    public function test_transaksi_kasir_tidak_memicu_telegram_jika_stok_masih_aman(): void
    {
        Config::set('services.telegram.admin_chat_id', '777888999');

        DB::table('pengaturan_notifikasi')->updateOrInsert(
            ['kunci' => 'stok_minimum'],
            ['nilai' => '1', 'diubah_pada' => now()]
        );

        $kodeBarang = 'BRG-AMN-' . time();
        DB::table('daftar_item')->insert([
            'kode_item' => $kodeBarang,
            'nama_item' => 'Penghapus Kenko Box',
            'jenis' => 'Alat Tulis',
            'stok' => 20,
            'batas_minimum' => 5,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'satuan' => 'pcs',
        ]);

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldNotReceive('kirimPesanTeks');

        $this->app->instance(TelegramBotService::class, $mockBot);

        /** @var TransaksiKasirService $transaksiService */
        $transaksiService = app(TransaksiKasirService::class);
        $transaksi = $transaksiService->prosesTransaksi([
            'items' => [
                [
                    'kode_item' => $kodeBarang,
                    'qty' => 2,
                ],
            ],
            'nominal_bayar' => 5000,
            'metode_bayar' => 'TUNAI',
        ], $this->kasir->id_pengguna);

        $this->assertNotNull($transaksi);
        $stokTerkini = (int) DB::table('daftar_item')->where('kode_item', $kodeBarang)->value('stok');
        $this->assertEquals(18, $stokTerkini);
    }

    /**
     * Transaksi kasir tidak mengirim peringatan jika saklar stok_minimum dinonaktifkan.
     */
    public function test_transaksi_kasir_tidak_memicu_telegram_jika_toggle_nonaktif(): void
    {
        Config::set('services.telegram.admin_chat_id', '777888999');

        DB::table('pengaturan_notifikasi')->updateOrInsert(
            ['kunci' => 'stok_minimum'],
            ['nilai' => '0', 'diubah_pada' => now()]
        );
        DB::table('pengaturan_notifikasi')->updateOrInsert(
            ['kunci' => 'notif_stok_minimum'],
            ['nilai' => '0', 'diubah_pada' => now()]
        );

        $kodeBarang = 'BRG-DIS-' . time();
        DB::table('daftar_item')->insert([
            'kode_item' => $kodeBarang,
            'nama_item' => 'Spidol Snowman Hitam',
            'jenis' => 'Alat Tulis',
            'stok' => 5,
            'batas_minimum' => 5,
            'harga_pokok' => 7000,
            'harga_jual' => 9000,
            'satuan' => 'pcs',
        ]);

        $mockBot = Mockery::mock(TelegramBotService::class);
        $mockBot->shouldNotReceive('kirimPesanTeks');

        $this->app->instance(TelegramBotService::class, $mockBot);

        /** @var TransaksiKasirService $transaksiService */
        $transaksiService = app(TransaksiKasirService::class);
        $transaksi = $transaksiService->prosesTransaksi([
            'items' => [
                [
                    'kode_item' => $kodeBarang,
                    'qty' => 1,
                ],
            ],
            'nominal_bayar' => 10000,
            'metode_bayar' => 'TUNAI',
        ], $this->kasir->id_pengguna);

        $this->assertNotNull($transaksi);
        $stokTerkini = (int) DB::table('daftar_item')->where('kode_item', $kodeBarang)->value('stok');
        $this->assertEquals(4, $stokTerkini);
    }
}
