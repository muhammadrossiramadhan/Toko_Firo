<?php

namespace Tests\Feature;

use App\Models\ItemBarang;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BarangCrudTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Admin aktif untuk pengujian.
     */
    protected function adminAktif(): Pengguna
    {
        return Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);
    }

    /**
     * Kasir aktif untuk pengujian otorisasi.
     */
    protected function kasirAktif(): Pengguna
    {
        return Pengguna::factory()->kasir()->create([
            'status' => 'AKTIF',
        ]);
    }

    /**
     * Test admin dapat menambah barang baru dan log_aktivitas ITEM_BARU tercatat.
     */
    public function test_admin_menambah_barang(): void
    {
        $admin = $this->adminAktif();
        $kode = 'TST_' . substr(uniqid(), -8);

        $response = $this->actingAs($admin)->post('/admin/barang', [
            'kode_item' => $kode,
            'nama_item' => 'Barang Uji CRUD Tambah',
            'jenis' => 'Alat Tulis',
            'merek' => 'Merk Uji',
            'rak' => 'R1',
            'satuan' => 'Pcs',
            'tipe_item' => 'Barang',
            'stok_awal' => 25,
            'harga_pokok' => 5000,
            'harga_jual' => 7500,
            'batas_minimum' => 10,
            'keterangan' => 'Keterangan uji barang baru',
        ]);

        $response->assertRedirect(route('admin.barang'));
        $response->assertSessionHas('sukses', 'Barang berhasil ditambahkan.');

        // Pastikan baris ada di daftar_item
        $this->assertDatabaseHas('daftar_item', [
            'kode_item' => $kode,
            'nama_item' => 'Barang Uji CRUD Tambah',
            'stok' => 25,
            'harga_pokok' => 5000,
            'harga_jual' => 7500,
        ]);

        // Pastikan log aktivitas tercatat
        $this->assertDatabaseHas('log_aktivitas', [
            'tipe_aksi' => 'ITEM_BARU',
            'id_pengguna' => $admin->id_pengguna,
        ]);
    }

    /**
     * Test kode kembar ditolak dengan pesan "Kode barang sudah dipakai."
     */
    public function test_kode_kembar_ditolak(): void
    {
        $admin = $this->adminAktif();
        $kode = 'TST_' . substr(uniqid(), -8);

        ItemBarang::create([
            'kode_item' => $kode,
            'nama_item' => 'Barang Asli',
            'tipe_item' => 'BARANG',
            'stok' => 10,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'batas_minimum' => 5,
        ]);

        $response = $this->actingAs($admin)->post('/admin/barang', [
            'kode_item' => $kode,
            'nama_item' => 'Barang Kembar',
            'tipe_item' => 'Barang',
            'stok_awal' => 5,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
        ]);

        $response->assertSessionHasErrors(['kode_item']);
        $this->assertStringContainsString('Kode barang sudah dipakai.', session('errors')->first('kode_item'));
    }

    /**
     * Test validasi input penambahan barang: nama kosong, kode > 20 karakter, stok > 1000, stok negatif, harga negatif.
     */
    public function test_validasi_input_penambahan_barang(): void
    {
        $admin = $this->adminAktif();

        // 1. Nama kosong
        $resNama = $this->actingAs($admin)->post('/admin/barang', [
            'kode_item' => 'TST_' . substr(uniqid(), -8),
            'nama_item' => '',
            'tipe_item' => 'Barang',
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
        ]);
        $resNama->assertSessionHasErrors(['nama_item']);

        // 2. Kode > 20 karakter
        $resKode = $this->actingAs($admin)->post('/admin/barang', [
            'kode_item' => 'TST_KODE_LEBIH_DARI_20_KARAKTER_PANJANG',
            'nama_item' => 'Barang Kode Panjang',
            'tipe_item' => 'Barang',
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
        ]);
        $resKode->assertSessionHasErrors(['kode_item']);

        // 3. Stok 1001 (melebihi batas konstanta 1000)
        $resStok1001 = $this->actingAs($admin)->post('/admin/barang', [
            'kode_item' => 'TST_' . substr(uniqid(), -8),
            'nama_item' => 'Barang Stok 1001',
            'tipe_item' => 'Barang',
            'stok_awal' => 1001,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
        ]);
        $resStok1001->assertSessionHasErrors(['stok_awal']);

        // 4. Stok negatif
        $resStokNegatif = $this->actingAs($admin)->post('/admin/barang', [
            'kode_item' => 'TST_' . substr(uniqid(), -8),
            'nama_item' => 'Barang Stok Negatif',
            'tipe_item' => 'Barang',
            'stok_awal' => -5,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
        ]);
        $resStokNegatif->assertSessionHasErrors(['stok_awal']);

        // 5. Harga negatif
        $resHargaNegatif = $this->actingAs($admin)->post('/admin/barang', [
            'kode_item' => 'TST_' . substr(uniqid(), -8),
            'nama_item' => 'Barang Harga Negatif',
            'tipe_item' => 'Barang',
            'stok_awal' => 10,
            'harga_pokok' => -1000,
            'harga_jual' => -2000,
        ]);
        $resHargaNegatif->assertSessionHasErrors(['harga_pokok', 'harga_jual']);
    }

    /**
     * Test tipe Jasa tersimpan dengan stok 999999.
     */
    public function test_tipe_jasa_stok_tersimpan_999999(): void
    {
        $admin = $this->adminAktif();
        $kode = 'TST_J_' . substr(uniqid(), -8);

        $response = $this->actingAs($admin)->post('/admin/barang', [
            'kode_item' => $kode,
            'nama_item' => 'Jasa Fotokopi Khusus',
            'tipe_item' => 'Jasa',
            'stok_awal' => 0,
            'harga_pokok' => 200,
            'harga_jual' => 500,
            'batas_minimum' => 5,
        ]);

        $response->assertRedirect(route('admin.barang'));

        $this->assertDatabaseHas('daftar_item', [
            'kode_item' => $kode,
            'tipe_item' => 'JASA',
            'stok' => 999999,
        ]);
    }

    /**
     * Test ubah data barang: kolom berubah, kode dan stok tidak berubah, tercatat log ITEM_DIUBAH.
     */
    public function test_ubah_data_barang(): void
    {
        $admin = $this->adminAktif();
        $kode = 'TST_' . substr(uniqid(), -8);

        ItemBarang::create([
            'kode_item' => $kode,
            'nama_item' => 'Nama Sebelum Diubah',
            'jenis' => 'Jenis Lama',
            'merek' => 'Merek Lama',
            'rak' => 'R1',
            'satuan' => 'Pcs',
            'tipe_item' => 'BARANG',
            'stok' => 50,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'batas_minimum' => 5,
        ]);

        $response = $this->actingAs($admin)->put('/admin/barang', [
            'kode_item' => $kode,
            'nama_item' => 'Nama Sesudah Diubah',
            'jenis' => 'Jenis Baru',
            'merek' => 'Merek Baru',
            'rak' => 'R2',
            'satuan' => 'Box',
            'tipe_item' => 'Barang',
            'batas_minimum' => 12,
            'keterangan' => 'Keterangan baru',
        ]);

        $response->assertRedirect(route('admin.barang'));
        $response->assertSessionHas('sukses', 'Data barang berhasil diubah.');

        $this->assertDatabaseHas('daftar_item', [
            'kode_item' => $kode,
            'nama_item' => 'Nama Sesudah Diubah',
            'jenis' => 'Jenis Baru',
            'merek' => 'Merek Baru',
            'rak' => 'R2',
            'satuan' => 'Box',
            'stok' => 50, // Stok tidak berubah
            'batas_minimum' => 12,
        ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'tipe_aksi' => 'ITEM_DIUBAH',
            'id_pengguna' => $admin->id_pengguna,
        ]);
    }

    /**
     * Test ubah harga barang: harga berubah, tercatat log HARGA_DIUBAH, harga jual di bawah harga pokok tetap tersimpan.
     */
    public function test_ubah_harga_barang_dan_harga_jual_lebih_rendah_tetap_tersimpan(): void
    {
        $admin = $this->adminAktif();
        $kode = 'TST_' . substr(uniqid(), -8);

        ItemBarang::create([
            'kode_item' => $kode,
            'nama_item' => 'Barang Ubah Harga',
            'tipe_item' => 'BARANG',
            'stok' => 10,
            'harga_pokok' => 5000,
            'harga_jual' => 8000,
            'batas_minimum' => 5,
        ]);

        // Harga jual lebih rendah dari harga pokok: Pokok 10000, Jual 7000
        $response = $this->actingAs($admin)->patch('/admin/barang/harga', [
            'kode_item' => $kode,
            'harga_pokok' => 10000,
            'harga_jual' => 7000,
        ]);

        $response->assertRedirect(route('admin.barang'));
        $response->assertSessionHas('sukses', 'Harga barang berhasil diubah.');

        $this->assertDatabaseHas('daftar_item', [
            'kode_item' => $kode,
            'harga_pokok' => 10000,
            'harga_jual' => 7000,
        ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'tipe_aksi' => 'HARGA_DIUBAH',
            'id_pengguna' => $admin->id_pengguna,
        ]);
    }

    /**
     * Test hapus barang tanpa riwayat transaksi: baris terhapus dan tercatat log ITEM_DIHAPUS.
     */
    public function test_hapus_barang_tanpa_transaksi(): void
    {
        $admin = $this->adminAktif();
        $kode = 'TST_' . substr(uniqid(), -8);

        ItemBarang::create([
            'kode_item' => $kode,
            'nama_item' => 'Barang Untuk Dihapus',
            'tipe_item' => 'BARANG',
            'stok' => 10,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'batas_minimum' => 5,
        ]);

        $response = $this->actingAs($admin)->delete('/admin/barang', [
            'kode_item' => $kode,
        ]);

        $response->assertRedirect(route('admin.barang'));
        $response->assertSessionHas('sukses', 'Barang berhasil dihapus.');

        $this->assertDatabaseMissing('daftar_item', [
            'kode_item' => $kode,
        ]);

        $this->assertDatabaseHas('log_aktivitas', [
            'tipe_aksi' => 'ITEM_DIHAPUS',
            'id_pengguna' => $admin->id_pengguna,
        ]);
    }

    /**
     * Test hapus barang yang memiliki riwayat transaksi ditolak dengan pesan yang sesuai.
     */
    public function test_hapus_barang_dengan_riwayat_transaksi_ditolak(): void
    {
        $admin = $this->adminAktif();
        $kode = 'TST_' . substr(uniqid(), -8);
        $kodePenjualan = 'PJ_' . substr(uniqid(), -10);

        ItemBarang::create([
            'kode_item' => $kode,
            'nama_item' => 'Barang Bertransaksi',
            'tipe_item' => 'BARANG',
            'stok' => 10,
            'harga_pokok' => 1000,
            'harga_jual' => 2000,
            'batas_minimum' => 5,
        ]);

        // Catat riwayat penjualan dan transaksi
        DB::table('daftar_penjualan')->insert([
            'kode_transaksi' => $kodePenjualan,
            'nama_pelanggan' => 'Pelanggan Test',
            'tanggal_transaksi' => now()->toDateString(),
            'total_bayar' => 2000,
        ]);

        DB::table('daftar_transaksi')->insert([
            'kode_item' => $kode,
            'kode_penjualan' => $kodePenjualan,
            'jumlah_item' => 1,
            'item_keluar' => 1,
            'subtotal' => 2000,
            'tanggal_log' => now(),
        ]);

        $response = $this->actingAs($admin)->delete('/admin/barang', [
            'kode_item' => $kode,
        ]);

        $response->assertSessionHas('error', 'Barang tidak bisa dihapus karena sudah pernah dijual.');

        // Pastikan baris barang masih ada di daftar_item
        $this->assertDatabaseHas('daftar_item', [
            'kode_item' => $kode,
        ]);
    }

    /**
     * Test otorisasi: KASIR mendapat 403 untuk keempat aksi CRUD.
     */
    public function test_kasir_mendapat_403_untuk_semua_aksi(): void
    {
        $kasir = $this->kasirAktif();
        $kode = 'TST_' . substr(uniqid(), -8);

        $this->actingAs($kasir)->post('/admin/barang', ['kode_item' => $kode])->assertStatus(403);
        $this->actingAs($kasir)->put('/admin/barang', ['kode_item' => $kode])->assertStatus(403);
        $this->actingAs($kasir)->patch('/admin/barang/harga', ['kode_item' => $kode])->assertStatus(403);
        $this->actingAs($kasir)->delete('/admin/barang', ['kode_item' => $kode])->assertStatus(403);
    }

    /**
     * Test otorisasi: tamu diarahkan ke route login untuk keempat aksi CRUD.
     */
    public function test_tamu_diarahkan_ke_login_untuk_semua_aksi(): void
    {
        $kode = 'TST_' . substr(uniqid(), -8);

        $this->post('/admin/barang', ['kode_item' => $kode])->assertRedirect(route('login'));
        $this->put('/admin/barang', ['kode_item' => $kode])->assertRedirect(route('login'));
        $this->patch('/admin/barang/harga', ['kode_item' => $kode])->assertRedirect(route('login'));
        $this->delete('/admin/barang', ['kode_item' => $kode])->assertRedirect(route('login'));
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

