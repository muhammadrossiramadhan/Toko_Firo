<?php

namespace Tests\Feature;

use App\Models\ItemBarang;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BarangDaftarTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Admin aktif yang digunakan untuk pengujian.
     */
    protected function adminAktif(): Pengguna
    {
        return Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);
    }

    /**
     * Test admin dapat melihat barang yang dibuat (kode dan nama tampil).
     */
    public function test_admin_melihat_barang_yang_dibuat(): void
    {
        $admin = $this->adminAktif();
        $kode = 'TL_' . substr(uniqid(), -8);

        ItemBarang::create([
            'kode_item' => $kode,
            'nama_item' => 'Buku Gambar Istimewa A3',
            'jenis' => 'Buku',
            'merek' => 'Sinar Dunia',
            'rak' => 'R1',
            'satuan' => 'Pcs',
            'harga_pokok' => 5000,
            'harga_jual' => 8000,
            'stok' => 20,
            'batas_minimum' => 5,
        ]);

        $response = $this->actingAs($admin)->get('/admin/barang?q=' . $kode);

        $response->assertStatus(200);
        $response->assertSee($kode);
        $response->assertSee('Buku Gambar Istimewa A3');
        $response->assertSee('Rp 8.000');
    }

    /**
     * Test paginasi 7: dengan 10 barang, halaman 1 menampilkan 7 dan halaman 2 menampilkan 3.
     */
    public function test_paginasi_tujuh_item_per_halaman(): void
    {
        $admin = $this->adminAktif();
        $jenisKhusus = 'JP_' . substr(uniqid(), -8);

        for ($i = 1; $i <= 10; $i++) {
            ItemBarang::create([
                'kode_item' => sprintf('PG_%s_%02d', substr(uniqid(), -4), $i),
                'nama_item' => sprintf('Barang Paginasi %02d', $i),
                'jenis' => $jenisKhusus,
                'harga_pokok' => 1000 * $i,
                'harga_jual' => 2000 * $i,
                'stok' => 10,
                'batas_minimum' => 5,
                'dibuat_pada' => now()->subMinutes(10 - $i),
            ]);
        }

        // Halaman 1
        $response1 = $this->actingAs($admin)->get('/admin/barang?jenis=' . $jenisKhusus . '&urut=terbaru');
        $response1->assertStatus(200);
        $response1->assertSee('Menampilkan 1-7 dari 10 barang');

        // Halaman 2
        $response2 = $this->actingAs($admin)->get('/admin/barang?jenis=' . $jenisKhusus . '&urut=terbaru&page=2');
        $response2->assertStatus(200);
        $response2->assertSee('Menampilkan 8-10 dari 10 barang');
    }

    /**
     * Test pencarian q mencocokkan nama dan kode, serta mengecualikan yang tidak cocok.
     */
    public function test_pencarian_q_cocok_nama_dan_kode_serta_mengecualikan_lainnya(): void
    {
        $admin = $this->adminAktif();
        $uniq = substr(uniqid(), -6);

        $kode1 = 'KMR_' . $uniq;
        $kode2 = 'KBR_' . $uniq;

        ItemBarang::create([
            'kode_item' => $kode1,
            'nama_item' => 'Pensil Khusus Merah ' . $uniq,
            'jenis' => 'Alat Tulis',
            'harga_pokok' => 2000,
            'harga_jual' => 3000,
            'stok' => 10,
            'batas_minimum' => 5,
        ]);

        ItemBarang::create([
            'kode_item' => $kode2,
            'nama_item' => 'Penghapus Karet Biru ' . $uniq,
            'jenis' => 'Alat Tulis',
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'stok' => 10,
            'batas_minimum' => 5,
        ]);

        // Cari berdasarkan potongan nama 'Merah'
        $responseNama = $this->actingAs($admin)->get('/admin/barang?q=' . urlencode('Merah ' . $uniq));
        $responseNama->assertStatus(200);
        $responseNama->assertSee('Pensil Khusus Merah ' . $uniq);
        $responseNama->assertDontSee('Penghapus Karet Biru ' . $uniq);

        // Cari berdasarkan kode barang
        $responseKode = $this->actingAs($admin)->get('/admin/barang?q=' . urlencode($kode2));
        $responseKode->assertStatus(200);
        $responseKode->assertSee('Penghapus Karet Biru ' . $uniq);
        $responseKode->assertDontSee('Pensil Khusus Merah ' . $uniq);
    }

    /**
     * Test filter jenis hanya menampilkan item dengan jenis tersebut.
     */
    public function test_filter_jenis_hanya_menampilkan_jenis_itu(): void
    {
        $admin = $this->adminAktif();
        $jenisA = 'JA_' . substr(uniqid(), -8);
        $jenisB = 'JB_' . substr(uniqid(), -8);

        $itemA = ItemBarang::create([
            'kode_item' => 'IA_' . substr(uniqid(), -6),
            'nama_item' => 'Produk Kategori A',
            'jenis' => $jenisA,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'stok' => 10,
            'batas_minimum' => 5,
        ]);

        $itemB = ItemBarang::create([
            'kode_item' => 'IB_' . substr(uniqid(), -6),
            'nama_item' => 'Produk Kategori B',
            'jenis' => $jenisB,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'stok' => 10,
            'batas_minimum' => 5,
        ]);

        $response = $this->actingAs($admin)->get('/admin/barang?jenis=' . $jenisA);
        $response->assertStatus(200);
        $response->assertSee($itemA->kode_item);
        $response->assertDontSee($itemB->kode_item);
    }

    /**
     * Test urutan stok terkecil dan urutan nama A-Z.
     */
    public function test_urutan_stok_dan_nama(): void
    {
        $admin = $this->adminAktif();
        $jenisUrut = 'JU_' . substr(uniqid(), -8);

        ItemBarang::create([
            'kode_item' => 'UC_' . substr(uniqid(), -6),
            'nama_item' => 'Crayon Warna Zebra',
            'jenis' => $jenisUrut,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'stok' => 50,
            'batas_minimum' => 5,
        ]);

        ItemBarang::create([
            'kode_item' => 'UB_' . substr(uniqid(), -6),
            'nama_item' => 'Buku Catatan Zebra',
            'jenis' => $jenisUrut,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'stok' => 5,
            'batas_minimum' => 5,
        ]);

        ItemBarang::create([
            'kode_item' => 'UA_' . substr(uniqid(), -6),
            'nama_item' => 'Alat Penggaris Zebra',
            'jenis' => $jenisUrut,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'stok' => 20,
            'batas_minimum' => 5,
        ]);

        // Urutan Stok Terkecil: 5 (Buku), 20 (Alat), 50 (Crayon)
        $responseStok = $this->actingAs($admin)->get('/admin/barang?jenis=' . $jenisUrut . '&urut=stok');
        $responseStok->assertStatus(200);
        $responseStok->assertSeeInOrder([
            'Buku Catatan Zebra',
            'Alat Penggaris Zebra',
            'Crayon Warna Zebra',
        ]);

        // Urutan Nama A-Z: Alat, Buku, Crayon
        $responseNama = $this->actingAs($admin)->get('/admin/barang?jenis=' . $jenisUrut . '&urut=nama');
        $responseNama->assertStatus(200);
        $responseNama->assertSeeInOrder([
            'Alat Penggaris Zebra',
            'Buku Catatan Zebra',
            'Crayon Warna Zebra',
        ]);
    }

    /**
     * Test status stok: 0 = Habis, sama dengan batas_minimum = Menipis, di atas batas_minimum = Aman.
     */
    public function test_status_stok_habis_menipis_dan_aman(): void
    {
        $admin = $this->adminAktif();
        $suffix = substr(uniqid(), -6);

        $habis = ItemBarang::create([
            'kode_item' => 'ST_H_' . $suffix,
            'nama_item' => 'Item Habis ' . $suffix,
            'jenis' => 'StatusTest',
            'stok' => 0,
            'batas_minimum' => 5,
        ]);

        $menipis = ItemBarang::create([
            'kode_item' => 'ST_M_' . $suffix,
            'nama_item' => 'Item Menipis ' . $suffix,
            'jenis' => 'StatusTest',
            'stok' => 5,
            'batas_minimum' => 5,
        ]);

        $aman = ItemBarang::create([
            'kode_item' => 'ST_A_' . $suffix,
            'nama_item' => 'Item Aman ' . $suffix,
            'jenis' => 'StatusTest',
            'stok' => 15,
            'batas_minimum' => 5,
        ]);

        // Cek accessor model
        $this->assertEquals('Habis', $habis->status_stok);
        $this->assertEquals('Menipis', $menipis->status_stok);
        $this->assertEquals('Aman', $aman->status_stok);

        // Cek tampilan HTML
        $response = $this->actingAs($admin)->get('/admin/barang?q=' . $suffix);
        $response->assertStatus(200);
        $response->assertSee('Habis');
        $response->assertSee('Menipis');
        $response->assertSee('Aman');
    }

    /**
     * Test halaman tidak memuat tag SVG.
     */
    public function test_halaman_tidak_memuat_svg(): void
    {
        $admin = $this->adminAktif();

        $response = $this->actingAs($admin)->get('/admin/barang');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }
}

