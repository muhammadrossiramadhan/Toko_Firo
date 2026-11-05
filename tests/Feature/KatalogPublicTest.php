<?php

namespace Tests\Feature;

use App\Models\ItemBarang;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KatalogPublicTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Admin dapat membuat barang dengan link shopee, gambar, dan deskripsi produk.
     */
    public function test_admin_dapat_menyimpan_barang_dengan_atribut_katalog(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $kode = 'KAT_' . substr(uniqid(), -8);

        $response = $this->actingAs($admin)->post('/admin/barang', [
            'kode_item' => $kode,
            'nama_item' => 'Buku Tulis Premium Katalog',
            'jenis' => 'Buku',
            'merek' => 'Firo Brand',
            'rak' => 'R1',
            'satuan' => 'Pcs',
            'tipe_item' => 'Barang',
            'stok_awal' => 15,
            'harga_pokok' => 4000,
            'harga_jual' => 6000,
            'batas_minimum' => 5,
            'keterangan' => 'Keterangan internal',
            'link_shopee' => 'https://shopee.co.id/toko-firo-buku',
            'deskripsi_produk' => 'Buku tulis kualitas kertas 80gsm premium.',
            'gambar_url' => 'https://example.com/buku.jpg',
        ]);

        $response->assertRedirect(route('admin.barang'));

        $this->assertDatabaseHas('daftar_item', [
            'kode_item' => $kode,
            'link_shopee' => 'https://shopee.co.id/toko-firo-buku',
            'deskripsi_produk' => 'Buku tulis kualitas kertas 80gsm premium.',
            'gambar_url' => 'https://example.com/buku.jpg',
        ]);
    }

    /**
     * Halaman katalog publik menampilkan barang, link shopee, dan deskripsi produk.
     */
    public function test_katalog_publik_menampilkan_data_katalog_dan_link_shopee(): void
    {
        $kode = 'PUB_' . substr(uniqid(), -8);

        ItemBarang::create([
            'kode_item' => $kode,
            'nama_item' => 'Pulpen Gel Hitam Publik',
            'jenis' => 'Alat Tulis',
            'merek' => 'Firo Gel',
            'rak' => 'A1',
            'satuan' => 'Pcs',
            'tipe_item' => 'BARANG',
            'harga_pokok' => 2000,
            'harga_jual' => 3500,
            'stok' => 30,
            'batas_minimum' => 5,
            'link_shopee' => 'https://shopee.co.id/pulpen-gel-firo',
            'deskripsi_produk' => 'Pulpen tinta gel hitam pekat anti macet.',
            'gambar_url' => 'https://example.com/pulpen.jpg',
        ]);

        $response = $this->get('/katalog?cari=' . $kode);

        $response->assertStatus(200);
        $response->assertSee('Pulpen Gel Hitam Publik');
        $response->assertSee('Rp 3.500');
        $response->assertSee('Pulpen tinta gel hitam pekat anti macet.');
        $response->assertSee('https://shopee.co.id/pulpen-gel-firo');
        $response->assertSee('Beli di Shopee');
    }

    /**
     * Admin dapat memperbarui atribut katalog pada barang yang sudah ada.
     */
    public function test_admin_dapat_memperbarui_atribut_katalog_barang(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $kode = 'UPD_' . substr(uniqid(), -8);

        ItemBarang::create([
            'kode_item' => $kode,
            'nama_item' => 'Penghapus Karet',
            'jenis' => 'Alat Tulis',
            'merek' => 'Firo Eraser',
            'rak' => 'B2',
            'satuan' => 'Pcs',
            'tipe_item' => 'BARANG',
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'stok' => 50,
            'batas_minimum' => 5,
        ]);

        $response = $this->actingAs($admin)->put('/admin/barang', [
            'kode_item' => $kode,
            'nama_item' => 'Penghapus Karet Diperbarui',
            'jenis' => 'Alat Tulis',
            'merek' => 'Firo Eraser Premium',
            'rak' => 'B2',
            'satuan' => 'Pcs',
            'tipe_item' => 'Barang',
            'keterangan' => 'Keterangan baru',
            'batas_minimum' => 10,
            'link_shopee' => 'https://shopee.co.id/penghapus-firo',
            'deskripsi_produk' => 'Penghapus bersih tanpa ampas.',
            'gambar_url' => 'https://example.com/penghapus.jpg',
        ]);

        $response->assertRedirect(route('admin.barang'));

        $this->assertDatabaseHas('daftar_item', [
            'kode_item' => $kode,
            'nama_item' => 'Penghapus Karet Diperbarui',
            'link_shopee' => 'https://shopee.co.id/penghapus-firo',
            'deskripsi_produk' => 'Penghapus bersih tanpa ampas.',
            'gambar_url' => 'https://example.com/penghapus.jpg',
        ]);
    }
}
