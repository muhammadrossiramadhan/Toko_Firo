<?php

namespace Tests\Feature;

use App\Models\ItemBarang;
use App\Models\Pengguna;
use App\Services\PengaturanTokoService;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CashierIntegrityTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $kasir;
    protected string $settingsFile;
    protected ?string $backupOriginalSettings = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settingsFile = storage_path('app/settings/shop_settings.json');
        if (file_exists($this->settingsFile)) {
            $this->backupOriginalSettings = file_get_contents($this->settingsFile);
        }

        $this->kasir = Pengguna::factory()->create([
            'nama' => 'Budi Santoso',
            'username' => 'budi_kasir',
            'role' => 'KASIR',
            'status' => 'AKTIF',
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->backupOriginalSettings !== null) {
            file_put_contents($this->settingsFile, $this->backupOriginalSettings);
        } elseif (file_exists($this->settingsFile)) {
            @unlink($this->settingsFile);
        }

        parent::tearDown();
    }

    /**
     * 1. Transaksi kasir berhasil dengan metode TUNAI dan QRIS.
     */
    public function test_transaksi_kasir_berhasil_dengan_metode_tunai_dan_qris(): void
    {
        $kodeItem = 'ITM-POS-' . rand(1000, 9999);
        DB::table('daftar_item')->insert([
            'kode_item' => $kodeItem,
            'nama_item' => 'Buku Tulis Kiky',
            'jenis' => 'Buku',
            'satuan' => 'pcs',
            'harga_pokok' => 4000,
            'harga_jual' => 6000,
            'stok' => 20,
            'batas_minimum' => 5,
            'dibuat_pada' => now(),
        ]);

        // A. Transaksi dengan metode TUNAI
        $responseTunai = $this->actingAs($this->kasir)->postJson(route('kasir.transaksi.bayar'), [
            'items' => [
                [
                    'kode_item' => $kodeItem,
                    'qty' => 2,
                ],
            ],
            'nominal_bayar' => 15000,
            'metode_bayar' => 'TUNAI',
        ]);

        $responseTunai->assertStatus(201);
        $responseTunai->assertJsonPath('status', 'success');

        $stokSetelahTunai = (int) DB::table('daftar_item')->where('kode_item', $kodeItem)->value('stok');
        $this->assertEquals(18, $stokSetelahTunai);

        // B. Transaksi dengan metode QRIS
        $responseQris = $this->actingAs($this->kasir)->postJson(route('kasir.transaksi.bayar'), [
            'items' => [
                [
                    'kode_item' => $kodeItem,
                    'qty' => 1,
                ],
            ],
            'metode_bayar' => 'QRIS',
        ]);

        $responseQris->assertStatus(201);
        $responseQris->assertJsonPath('status', 'success');

        $stokSetelahQris = (int) DB::table('daftar_item')->where('kode_item', $kodeItem)->value('stok');
        $this->assertEquals(17, $stokSetelahQris);
    }

    /**
     * 2. Permintaan transaksi dengan metode TRANSFER ditolak dengan status HTTP 422.
     */
    public function test_permintaan_transaksi_dengan_metode_transfer_ditolak_status_422(): void
    {
        $kodeItem = 'ITM-TRF-' . rand(1000, 9999);
        DB::table('daftar_item')->insert([
            'kode_item' => $kodeItem,
            'nama_item' => 'Pulpen Gel Hitam',
            'jenis' => 'ATK',
            'satuan' => 'pcs',
            'harga_pokok' => 2000,
            'harga_jual' => 3500,
            'stok' => 15,
            'batas_minimum' => 3,
            'dibuat_pada' => now(),
        ]);

        $response = $this->actingAs($this->kasir)->postJson(route('kasir.transaksi.bayar'), [
            'items' => [
                [
                    'kode_item' => $kodeItem,
                    'qty' => 1,
                ],
            ],
            'nominal_bayar' => 3500,
            'metode_bayar' => 'TRANSFER',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['metode_bayar']);
    }

    /**
     * 3. Database tanpa produk menampilkan tampilan state kosong tanpa produk dummy tiruan.
     */
    public function test_database_tanpa_produk_menampilkan_empty_state_tanpa_produk_dummy(): void
    {
        // Kosongkan tabel daftar_item untuk verifikasi empty state
        DB::table('daftar_transaksi')->delete();
        DB::table('daftar_item')->delete();

        $response = $this->actingAs($this->kasir)->get(route('kasir.stok'));

        $response->assertStatus(200);
        // Pastikan tidak ada data dummy tiruan (Pensil 2B Faber, BRG-007, Kertas HVS dummy 47 total)
        $response->assertDontSee('Pensil 2B Faber');
        $response->assertDontSee('BRG-007');
        $response->assertSee('Menampilkan 0-0 dari 0 barang');
    }

    /**
     * 4. Tampilan dashboard dan header kasir merender nama login tanpa residu nama "Rina".
     */
    public function test_tampilan_dashboard_dan_header_kasir_merender_nama_login_tanpa_residu_rina(): void
    {
        $routes = [
            route('kasir.dashboard'),
            route('kasir.transaksi'),
            route('kasir.stok'),
            route('kasir.saldo'),
            route('kasir.info-barang'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->kasir)->get($url);
            $response->assertStatus(200);
            $response->assertSee('Budi Santoso');
            $response->assertDontSee('Rina Putri');
            $response->assertDontSee('Halo, Rina');
        }
    }

    /**
     * 5. Endpoint cetak struk menghasilkan kode nota yang disanitasi dan membaca identitas toko dari pengaturan.
     */
    public function test_endpoint_cetak_struk_menghasilkan_kode_nota_tersanitasi_dan_identitas_toko_pengaturan(): void
    {
        /** @var PengaturanTokoService $tokoService */
        $tokoService = app(PengaturanTokoService::class);
        $tokoService->updateSettings([
            'nama_toko' => 'Toko ATK Mitra Firo',
            'header_struk' => 'Solusi Kebutuhan Kantor Terlengkap',
            'alamat' => 'Jl. Pahlawan No. 45, Kraksaan',
            'telepon' => '0899-1122-3344',
            'footer_struk' => 'Barang yang dibeli tidak dapat diuangkan kembali.',
        ]);

        $kodeTransaksi = 'TRX<b id=x>' . rand(10, 99);

        DB::table('daftar_penjualan')->insert([
            'kode_transaksi' => $kodeTransaksi,
            'nama_pelanggan' => 'Pelanggan <test>',
            'tanggal_transaksi' => now()->toDateString(),
            'total_bayar' => 25000,
            'id_kasir' => $this->kasir->id_pengguna,
            'metode_bayar' => 'TUNAI',
        ]);

        $response = $this->actingAs($this->kasir)->get(route('kasir.transaksi.cetak', ['nota' => $kodeTransaksi]));

        $response->assertStatus(200);

        // Verifikasi tidak ada tag HTML mentah (XSS dicegah)
        $response->assertDontSee('<b id=x>', false);
        $response->assertDontSee('<script>alert(1)</script>', false);

        // Verifikasi identitas toko dinamis dari shop settings
        $response->assertSee('Toko ATK Mitra Firo');
        $response->assertSee('Solusi Kebutuhan Kantor Terlengkap');
        $response->assertSee('Jl. Pahlawan No. 45, Kraksaan');
        $response->assertSee('0899-1122-3344');
        $response->assertSee('Barang yang dibeli tidak dapat diuangkan kembali.');
    }

    /**
     * 6. Tombol ekspor saldo berfungsi menghasilkan respons dokumen / dispatch tanpa galat 404/500.
     */
    public function test_tombol_ekspor_saldo_menghasilkan_dokumen_pdf_dan_dispatch_telegram_tanpa_galat(): void
    {
        // A. Unduh PDF Saldo Kasir
        $responsePdf = $this->actingAs($this->kasir)->get(route('kasir.saldo.pdf'));

        $responsePdf->assertStatus(200);
        $responsePdf->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $responsePdf->getContent());

        // B. Kirim Saldo Kasir ke Telegram
        Config::set('services.telegram.admin_chat_id', '99887766');

        $mockBot = \Mockery::mock(TelegramBotService::class);
        $mockBot->shouldReceive('sendDocument')
            ->once()
            ->andReturn(['ok' => true]);

        $this->app->instance(TelegramBotService::class, $mockBot);

        $responseTelegram = $this->actingAs($this->kasir)->post(route('kasir.saldo.telegram'));

        $responseTelegram->assertStatus(302);
        $responseTelegram->assertSessionHas('success');
    }
}
