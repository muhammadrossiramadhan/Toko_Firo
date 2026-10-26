<?php

namespace Tests\Feature\Kasir;

use App\Models\ItemBarang;
use App\Models\Pengguna;
use App\Services\Kasir\StokKasirService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StokKasirTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Uji otentikasi: kasir yang belum login dialihkan ke /login atau /masuk.
     */
    public function test_kasir_belum_login_dialihkan_ke_login(): void
    {
        // Akses endpoint cari AJAX tanpa login wajib dialihkan ke login
        $responseCari = $this->get('/kasir/stok/cari');
        $this->assertTrue(
            $responseCari->isRedirect(url('/masuk')) ||
            $responseCari->isRedirect(url('/login')) ||
            $responseCari->isRedirect(route('login'))
        );
    }

    /**
     * Uji kasir yang terautentikasi dapat mengakses halaman stok kasir.
     */
    public function test_kasir_terautentikasi_dapat_mengakses_stok(): void
    {
        /** @var Pengguna $kasir */
        $kasir = Pengguna::factory()->kasir()->create();

        $response = $this->actingAs($kasir)->get('/kasir/stok');

        $response->assertStatus(200);
    }

    /**
     * Uji pencarian barang berdasarkan nama dan barcode via endpoint cariAjax.
     */
    public function test_pencarian_barang_berdasarkan_nama_dan_barcode(): void
    {
        /** @var Pengguna $kasir */
        $kasir = Pengguna::factory()->kasir()->create();

        // Pastikan ada item pengujian
        ItemBarang::where('kode_item', 'TEST-STK-01')->delete();
        ItemBarang::create([
            'kode_item' => 'TEST-STK-01',
            'nama_item' => 'Buku Gambar A3 Khusus Kasir',
            'jenis' => 'Buku',
            'satuan' => 'pcs',
            'harga_pokok' => 5000,
            'harga_jual' => 8000,
            'stok' => 20,
            'batas_minimum' => 5,
        ]);

        // 1. Cari berdasarkan nama
        $responseNama = $this->actingAs($kasir)->getJson(route('kasir.stok.cari', ['q' => 'Gambar A3']));
        $responseNama->assertStatus(200);
        $responseNama->assertJsonPath('status', 'success');
        $dataNama = $responseNama->json('data');
        $this->assertNotEmpty($dataNama);
        $this->assertEquals('TEST-STK-01', $dataNama[0]['kode_barang']);
        $this->assertEquals('Buku Gambar A3 Khusus Kasir', $dataNama[0]['nama_barang']);
        $this->assertEquals('TEST-STK-01', $dataNama[0]['barcode']);

        // 2. Cari berdasarkan barcode / kode
        $responseBarcode = $this->actingAs($kasir)->getJson(route('kasir.stok.cari', ['barcode' => 'TEST-STK-01']));
        $responseBarcode->assertStatus(200);
        $responseBarcode->assertJsonPath('status', 'success');
        $dataBarcode = $responseBarcode->json('data');
        $this->assertNotEmpty($dataBarcode);
        $this->assertEquals('TEST-STK-01', $dataBarcode[0]['kode_barang']);
    }

    /**
     * Uji akurasi kalkulasi komputasi status stok ('aman', 'menipis', 'habis').
     */
    public function test_akurasi_kalkulasi_komputasi_status_stok(): void
    {
        // Siapkan 3 data dengan kondisi stok berbeda
        ItemBarang::whereIn('kode_item', ['TEST-AMAN', 'TEST-MENIPIS', 'TEST-HABIS'])->delete();

        // 1. Aman: stok > batas_minimum (10 > 5)
        ItemBarang::create([
            'kode_item' => 'TEST-AMAN',
            'nama_item' => 'Barang Stok Aman',
            'harga_jual' => 10000,
            'stok' => 10,
            'batas_minimum' => 5,
        ]);

        // 2. Menipis: stok <= batas_minimum && stok > 0 (3 <= 5)
        ItemBarang::create([
            'kode_item' => 'TEST-MENIPIS',
            'nama_item' => 'Barang Stok Menipis',
            'harga_jual' => 10000,
            'stok' => 3,
            'batas_minimum' => 5,
        ]);

        // 3. Habis: stok == 0
        ItemBarang::create([
            'kode_item' => 'TEST-HABIS',
            'nama_item' => 'Barang Stok Habis',
            'harga_jual' => 10000,
            'stok' => 0,
            'batas_minimum' => 5,
        ]);

        $service = app(StokKasirService::class);

        // Periksa item aman
        $hasilAman = $service->getDaftarBarang(['q' => 'TEST-AMAN'])->items();
        $this->assertNotEmpty($hasilAman);
        $this->assertEquals('aman', $hasilAman[0]->status_stok);

        // Periksa item menipis
        $hasilMenipis = $service->getDaftarBarang(['q' => 'TEST-MENIPIS'])->items();
        $this->assertNotEmpty($hasilMenipis);
        $this->assertEquals('menipis', $hasilMenipis[0]->status_stok);

        // Periksa item habis
        $hasilHabis = $service->getDaftarBarang(['q' => 'TEST-HABIS'])->items();
        $this->assertNotEmpty($hasilHabis);
        $this->assertEquals('habis', $hasilHabis[0]->status_stok);
    }

    /**
     * Uji method cekKetersediaan mengembalikan respon boolean yang valid.
     */
    public function test_method_cek_ketersediaan_mengembalikan_respon_boolean_valid(): void
    {
        ItemBarang::where('kode_item', 'TEST-CEK-01')->delete();
        ItemBarang::create([
            'kode_item' => 'TEST-CEK-01',
            'nama_item' => 'Barang Uji Ketersediaan',
            'harga_jual' => 5000,
            'stok' => 15,
            'batas_minimum' => 5,
        ]);

        $service = app(StokKasirService::class);

        // Skenario 1: Jumlah diminta kurang dari stok (Tersedia)
        $resCukup = $service->cekKetersediaan('TEST-CEK-01', 5);
        $this->assertIsArray($resCukup);
        $this->assertTrue($resCukup['tersedia']);
        $this->assertEquals(15, $resCukup['sisa_stok']);
        $this->assertNotNull($resCukup['barang']);

        // Skenario 2: Jumlah diminta pas dengan stok (Tersedia)
        $resPas = $service->cekKetersediaan('TEST-CEK-01', 15);
        $this->assertTrue($resPas['tersedia']);
        $this->assertEquals(15, $resPas['sisa_stok']);

        // Skenario 3: Jumlah diminta melebihi stok (Tidak Tersedia)
        $resKurang = $service->cekKetersediaan('TEST-CEK-01', 20);
        $this->assertFalse($resKurang['tersedia']);
        $this->assertEquals(15, $resKurang['sisa_stok']);

        // Skenario 4: Barang tidak ditemukan (Tidak Tersedia)
        $resTidakAda = $service->cekKetersediaan('KODE-TIDAK-VALID-999', 1);
        $this->assertFalse($resTidakAda['tersedia']);
        $this->assertEquals(0, $resTidakAda['sisa_stok']);
        $this->assertNull($resTidakAda['barang']);
    }
}

