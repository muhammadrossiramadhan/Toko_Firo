<?php

namespace Tests\Feature;

use App\Models\DaftarPenjualan;
use App\Models\Pengguna;
use App\Services\PengaturanTokoService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminPengaturanTokoTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $admin;
    protected string $settingsFile;
    protected ?string $backupOriginalSettings = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settingsFile = storage_path('app/settings/shop_settings.json');
        if (file_exists($this->settingsFile)) {
            $this->backupOriginalSettings = file_get_contents($this->settingsFile);
        }

        $this->admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
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
     * a. Akses halaman pengaturan toko menampilkan form dan data yang tersimpan.
     */
    public function test_akses_halaman_pengaturan_toko_menampilkan_form_dan_data_tersimpan(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/pengaturan-toko');

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Toko');
        $response->assertSee('Informasi Toko');
        $response->assertSee('Toko Firo');
        $response->assertSee('081234567890');
        $response->assertSee('Pengaturan Struk');
        $response->assertSee('TOKO FIRO — ATK & Fotocopy');
        $response->assertSee('Terima kasih telah berbelanja!');
        $response->assertSee('WhatsApp Bot');
        $response->assertSee('Nonaktif');
        $response->assertDontSee('<svg', false);
    }

    /**
     * b. Pembaruan data identitas toko dan teks struk berhasil disimpan dan divalidasi.
     */
    public function test_pembaruan_data_identitas_toko_dan_teks_struk_berhasil_disimpan(): void
    {
        $payload = [
            'nama_toko'    => 'Toko Firo Mega Mart',
            'alamat'       => 'Jl. Soekarno Hatta No. 99, Malang',
            'telepon'      => '081987654321',
            'header_struk' => 'FIRO MEGA MART — PUSAT ATK TERLENGKAP',
            'footer_struk' => 'Barang yang dibeli tidak dapat ditukar kecuali cacat pabrik.',
        ];

        $response = $this->actingAs($this->admin)->put('/admin/pengaturan-toko', $payload);

        $response->assertRedirect('/admin/pengaturan-toko');
        $response->assertSessionHas('success', 'Pengaturan toko dan struk berhasil diperbarui.');

        // Verifikasi pada halaman setelah pembaruan
        $responseGet = $this->actingAs($this->admin)->get('/admin/pengaturan-toko');
        $responseGet->assertStatus(200);
        $responseGet->assertSee('Toko Firo Mega Mart');
        $responseGet->assertSee('Jl. Soekarno Hatta No. 99, Malang');
        $responseGet->assertSee('081987654321');
        $responseGet->assertSee('FIRO MEGA MART — PUSAT ATK TERLENGKAP');
        $responseGet->assertSee('Barang yang dibeli tidak dapat ditukar kecuali cacat pabrik.');
    }

    /**
     * b. (Variant) Pembaruan via AJAX JSON mengembalikan respons JSON valid.
     */
    public function test_pembaruan_via_ajax_mengembalikan_json_sukses(): void
    {
        $payload = [
            'nama_toko'    => 'Toko Firo Official',
            'alamat'       => 'Jl. MT Haryono No. 10, Malang',
            'telepon'      => '081122334455',
            'header_struk' => 'TOKO FIRO OFFICIAL STORE',
            'footer_struk' => 'Terima kasih atas kepercayaan Anda.',
        ];

        $response = $this->actingAs($this->admin)->putJson('/admin/pengaturan-toko', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Pengaturan toko dan struk berhasil diperbarui.',
            'data' => [
                'nama_toko' => 'Toko Firo Official',
                'telepon' => '081122334455',
            ],
        ]);
    }

    /**
     * b. (Negative) Validasi menolak data pengaturan toko yang tidak lengkap.
     */
    public function test_validasi_menolak_data_pengaturan_toko_yang_tidak_lengkap(): void
    {
        $payload = [
            'nama_toko' => '',
            'alamat' => '',
            'telepon' => '',
        ];

        $response = $this->actingAs($this->admin)->put('/admin/pengaturan-toko', $payload);

        $response->assertSessionHasErrors(['nama_toko', 'alamat', 'telepon']);
    }

    /**
     * c. Data baru langsung terefleksi pada pemanggilan service dan cetak struk kasir.
     */
    public function test_data_baru_langsung_terefleksi_pada_pemanggilan_service_dan_cetak_struk(): void
    {
        /** @var PengaturanTokoService $service */
        $service = app(PengaturanTokoService::class);
        $service->updateSettings([
            'nama_toko'    => 'Toko Firo Prima',
            'alamat'       => 'Jl. Veteran No. 5, Malang',
            'telepon'      => '085566778899',
            'header_struk' => 'FIRO PRIMA — FOTOCOPY & PERCETAKAN',
            'footer_struk' => 'Struk ini adalah bukti pembayaran yang sah.',
        ]);

        $this->assertEquals('Toko Firo Prima', $service->getNamaToko());
        $this->assertEquals('FIRO PRIMA — FOTOCOPY & PERCETAKAN', $service->getHeaderStruk());
        $this->assertEquals('Struk ini adalah bukti pembayaran yang sah.', $service->getFooterStruk());

        // Buat record penjualan uji
        $nota = 'TRX-TEST-' . time();
        DaftarPenjualan::create([
            'kode_transaksi'    => $nota,
            'nama_pelanggan'    => 'Pelanggan Setia',
            'tanggal_transaksi' => now()->toDateString(),
            'total_bayar'       => 25000,
            'metode_bayar'      => 'TUNAI',
            'id_kasir'          => $this->admin->id_pengguna,
        ]);

        $responseStruk = $this->actingAs($this->admin)->get("/kasir/transaksi/{$nota}/cetak");

        $responseStruk->assertStatus(200);
        $responseStruk->assertSee('Toko Firo Prima');
        $responseStruk->assertSee('FIRO PRIMA — FOTOCOPY & PERCETAKAN');
        $responseStruk->assertSee('085566778899');
        $responseStruk->assertSee('Struk ini adalah bukti pembayaran yang sah.');
    }
}

