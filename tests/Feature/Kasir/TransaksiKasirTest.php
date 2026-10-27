<?php

namespace Tests\Feature\Kasir;

use App\Models\DaftarItem;
use App\Models\DaftarPenjualan;
use App\Models\DaftarTransaksi;
use App\Models\ItemBarang;
use App\Models\Pengguna;
use App\Services\Kasir\TransaksiKasirService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TransaksiKasirTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Bersihkan data uji jika pernah tersimpan
        DaftarTransaksi::where('kode_item', 'like', 'TRX-TEST-%')->delete();
        DaftarPenjualan::where('kode_transaksi', 'like', 'TRX-TEST-%')->delete();
        ItemBarang::where('kode_item', 'like', 'TRX-TEST-%')->delete();
    }

    /**
     * Uji otentikasi: kasir yang belum login dialihkan ke halaman login saat simpan transaksi.
     */
    public function test_kasir_belum_login_dialihkan_ke_login(): void
    {
        $response = $this->postJson('/kasir/transaksi/simpan', [
            'items' => [
                ['barang_id' => 'BRG-001', 'qty' => 1],
            ],
            'bayar' => 10000,
        ]);

        $this->assertTrue(
            $response->status() === 401 ||
            $response->isRedirect(url('/masuk')) ||
            $response->isRedirect(url('/login')) ||
            $response->isRedirect(route('login'))
        );
    }

    /**
     * Uji transaksi sukses:
     * - Stok barang berkurang sesuai kuantitas.
     * - Record penjualan dan detail_penjualan tersimpan.
     * - Kembalian terhitung akurat.
     */
    public function test_transaksi_berhasil_dan_stok_berkurang_serta_kembalian_akurat(): void
    {
        /** @var Pengguna $kasir */
        $kasir = Pengguna::factory()->kasir()->create();

        // Siapkan barang uji
        $item1 = ItemBarang::create([
            'kode_item' => 'TRX-TEST-01',
            'nama_item' => 'Buku Tulis Uji Transaksi',
            'jenis' => 'Buku',
            'satuan' => 'pcs',
            'harga_pokok' => 3000,
            'harga_jual' => 5000,
            'stok' => 20,
            'batas_minimum' => 5,
        ]);

        $item2 = ItemBarang::create([
            'kode_item' => 'TRX-TEST-02',
            'nama_item' => 'Pulpen Gel Uji Transaksi',
            'jenis' => 'Alat Tulis',
            'satuan' => 'pcs',
            'harga_pokok' => 2000,
            'harga_jual' => 3000,
            'stok' => 15,
            'batas_minimum' => 5,
        ]);

        // Total belanja: (2 * 5000) + (3 * 3000) - 2000 diskon = 10000 + 9000 - 2000 = 17000
        // Bayar: 20000 -> Kembalian = 3000
        $payload = [
            'items' => [
                ['barang_id' => 'TRX-TEST-01', 'qty' => 2],
                ['barang_id' => 'TRX-TEST-02', 'qty' => 3],
            ],
            'diskon' => 2000,
            'bayar' => 20000,
            'metode_bayar' => 'TUNAI',
            'nama_pelanggan' => 'Pelanggan Setia',
        ];

        $response = $this->actingAs($kasir)->postJson(route('kasir.transaksi.simpan'), $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonStructure([
            'status',
            'message',
            'nomor_nota',
            'kembalian',
            'url_cetak',
            'data',
        ]);

        $nomorNota = $response->json('nomor_nota');
        $kembalian = $response->json('kembalian');

        // Kembalian harus persis 3000
        $this->assertEquals(3000, $kembalian);

        // Periksa pengurangan stok di database
        $this->assertEquals(18, (int) ItemBarang::where('kode_item', 'TRX-TEST-01')->value('stok'));
        $this->assertEquals(12, (int) ItemBarang::where('kode_item', 'TRX-TEST-02')->value('stok'));

        // Periksa record di tabel daftar_penjualan
        $penjualan = DaftarPenjualan::where('kode_transaksi', $nomorNota)->first();
        $this->assertNotNull($penjualan);
        $this->assertEquals(17000, (float) $penjualan->total_bayar);
        $this->assertEquals('Pelanggan Setia', $penjualan->nama_pelanggan);
        $this->assertEquals('TUNAI', $penjualan->metode_bayar);

        // Periksa detail di tabel daftar_transaksi
        $detailTransaksi = DaftarTransaksi::where('kode_penjualan', $nomorNota)->get();
        $this->assertCount(2, $detailTransaksi);

        $detail1 = $detailTransaksi->firstWhere('kode_item', 'TRX-TEST-01');
        $this->assertNotNull($detail1);
        $this->assertEquals(2, $detail1->item_keluar);
        $this->assertEquals(10000, (float) $detail1->subtotal);

        $detail2 = $detailTransaksi->firstWhere('kode_item', 'TRX-TEST-02');
        $this->assertNotNull($detail2);
        $this->assertEquals(3, $detail2->item_keluar);
        $this->assertEquals(9000, (float) $detail2->subtotal);

        // Uji cetak struk untuk transaksi yang baru dibuat
        $responseCetak = $this->actingAs($kasir)->get(route('kasir.transaksi.cetak', ['nota' => $nomorNota]));
        $responseCetak->assertStatus(200);
        $responseCetak->assertSee($nomorNota);
        $responseCetak->assertSee('Buku Tulis Uji Transaksi');
        $responseCetak->assertSee('Pulpen Gel Uji Transaksi');
        $responseCetak->assertSee('TOTAL BAYAR');
    }

    /**
     * Uji penolakan transaksi: validasi gagal jika kuantitas melebihi stok yang tersedia.
     * Database harus rollback penuh dan stok barang tidak berkurang.
     */
    public function test_penolakan_transaksi_jika_stok_tidak_mencukupi(): void
    {
        /** @var Pengguna $kasir */
        $kasir = Pengguna::factory()->kasir()->create();

        // Barang stok hanya 5
        ItemBarang::create([
            'kode_item' => 'TRX-TEST-03',
            'nama_item' => 'Penghapus Kritis Uji',
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'stok' => 5,
            'batas_minimum' => 2,
        ]);

        $payload = [
            'items' => [
                ['barang_id' => 'TRX-TEST-03', 'qty' => 10], // Minta 10 padahal stok 5
            ],
            'bayar' => 50000,
        ];

        $response = $this->actingAs($kasir)->postJson(route('kasir.transaksi.simpan'), $payload);

        $response->assertStatus(422);
        $response->assertJsonPath('status', 'error');

        // Pastikan stok tidak berkurang (rollback terjaga)
        $this->assertEquals(5, (int) ItemBarang::where('kode_item', 'TRX-TEST-03')->value('stok'));

        // Pastikan tidak ada transaksi yang tersimpan
        $this->assertEquals(0, DaftarPenjualan::where('kode_transaksi', 'like', 'PJ-' . date('Ymd') . '-%')->whereHas('transaksi', function ($q) {
            $q->where('kode_item', 'TRX-TEST-03');
        })->count());
    }

    /**
     * Uji penolakan transaksi jika uang pembayaran kurang dari total belanja.
     */
    public function test_penolakan_transaksi_jika_uang_bayar_kurang(): void
    {
        /** @var Pengguna $kasir */
        $kasir = Pengguna::factory()->kasir()->create();

        ItemBarang::create([
            'kode_item' => 'TRX-TEST-04',
            'nama_item' => 'Spidol Warna Uji',
            'harga_pokok' => 5000,
            'harga_jual' => 8000,
            'stok' => 10,
            'batas_minimum' => 2,
        ]);

        // Total belanja: 2 * 8000 = 16000, tapi bayar hanya 10000
        $payload = [
            'items' => [
                ['barang_id' => 'TRX-TEST-04', 'qty' => 2],
            ],
            'bayar' => 10000,
        ];

        $response = $this->actingAs($kasir)->postJson(route('kasir.transaksi.simpan'), $payload);

        $response->assertStatus(422);
        $response->assertJsonPath('status', 'error');

        // Stok tidak boleh berkurang
        $this->assertEquals(10, (int) ItemBarang::where('kode_item', 'TRX-TEST-04')->value('stok'));
    }

    /**
     * Uji generator nomor nota menghasilkan nomor unik dan berurutan dengan format PJ-YYYYMMDD-XXXX.
     */
    public function test_generate_nomor_nota_harian(): void
    {
        $service = app(TransaksiKasirService::class);

        $nota1 = $service->generateNomorNota();
        $this->assertStringStartsWith('PJ-' . date('Ymd') . '-', $nota1);

        // Simpan 1 transaksi dummy untuk memastikan urutan berikutnya bertambah
        DaftarPenjualan::create([
            'kode_transaksi' => $nota1,
            'nama_pelanggan' => 'Dummy Pelanggan',
            'tanggal_transaksi' => now()->toDateString(),
            'total_bayar' => 1000,
        ]);

        $nota2 = $service->generateNomorNota();
        $this->assertNotEquals($nota1, $nota2);
        $this->assertStringStartsWith('PJ-' . date('Ymd') . '-', $nota2);
    }

    /**
     * Uji endpoint POST /kasir/transaksi/keranjang untuk penambahan item dan validasi stok.
     */
    public function test_update_keranjang_berhasil_dan_validasi_stok(): void
    {
        $item = ItemBarang::create([
            'kode_item' => 'TRX-TEST-05',
            'nama_item' => 'Map Kancing Uji',
            'harga_pokok' => 1500,
            'harga_jual' => 3000,
            'stok' => 5,
            'batas_minimum' => 2,
        ]);

        // 1. Tambah 2 item ke keranjang (stok 5 -> cukup)
        $response = $this->postJson(route('kasir.transaksi.keranjang'), [
            'action' => 'tambah',
            'kode_item' => 'TRX-TEST-05',
            'qty' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('total_belanja', 6000);
        $response->assertJsonPath('total_item', 2);

        // 2. Tambah lagi 10 item (total 12 > stok 5 -> harus ditolak 422)
        $responseOver = $this->postJson(route('kasir.transaksi.keranjang'), [
            'action' => 'tambah',
            'kode_item' => 'TRX-TEST-05',
            'qty' => 10,
        ]);

        $responseOver->assertStatus(422);
        $responseOver->assertJsonPath('status', 'error');

        // 3. Kosongkan keranjang
        $responseKosong = $this->postJson(route('kasir.transaksi.keranjang'), [
            'action' => 'kosongkan',
        ]);

        $responseKosong->assertStatus(200);
        $responseKosong->assertJsonPath('total_belanja', 0);
        $responseKosong->assertJsonPath('total_item', 0);
    }

    /**
     * Uji alur pembayaran via POST /kasir/transaksi/bayar memotong stok dan mencatat transaksi di MariaDB.
     */
    public function test_checkout_bayar_via_endpoint_bayar_berhasil(): void
    {
        /** @var Pengguna $kasir */
        $kasir = Pengguna::factory()->kasir()->create();

        $item = ItemBarang::create([
            'kode_item' => 'TRX-TEST-06',
            'nama_item' => 'Staples Max Uji',
            'harga_pokok' => 8000,
            'harga_jual' => 12000,
            'stok' => 10,
            'batas_minimum' => 2,
        ]);

        $payload = [
            'items' => [
                ['barang_id' => 'TRX-TEST-06', 'qty' => 3],
            ],
            'bayar' => 40000,
            'metode_bayar' => 'TUNAI',
        ];

        $response = $this->actingAs($kasir)->postJson(route('kasir.transaksi.bayar'), $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('kembalian', 4000); // 40000 - (3 * 12000 = 36000) = 4000

        // Pastikan stok di database terpotong: 10 - 3 = 7
        $this->assertEquals(7, (int) ItemBarang::where('kode_item', 'TRX-TEST-06')->value('stok'));

        // Pastikan header transaksi tercatat di daftar_penjualan
        $nota = $response->json('nomor_nota');
        $this->assertDatabaseHas('daftar_penjualan', [
            'kode_transaksi' => $nota,
            'total_bayar' => 36000,
        ]);

        // Pastikan baris item tercatat di daftar_transaksi
        $this->assertDatabaseHas('daftar_transaksi', [
            'kode_penjualan' => $nota,
            'kode_item' => 'TRX-TEST-06',
            'item_keluar' => 3,
            'subtotal' => 36000,
        ]);

        // Pastikan saldo_harian terisi/bertambah untuk transaksi TUNAI
        $this->assertDatabaseHas('saldo_harian', [
            'tanggal' => now()->toDateString(),
        ]);
    }
}

