<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoBarangTest extends TestCase
{
    /**
     * a. GET /demo/barang mengembalikan status 200.
     */
    public function test_demo_barang_returns_200(): void
    {
        $response = $this->get('/demo/barang');

        $response->assertStatus(200);
    }

    /**
     * b. Halaman 1 memuat "Menampilkan 1-7 dari 47 barang".
     */
    public function test_halaman_1_menampilkan_rentang_yang_benar(): void
    {
        $response = $this->get('/demo/barang?page=1');

        $response->assertStatus(200);
        $response->assertSee('Menampilkan 1-7 dari 47 barang');
    }

    /**
     * c. Halaman 7 (?page=7) memuat "Menampilkan 43-47 dari 47 barang".
     */
    public function test_halaman_7_menampilkan_rentang_yang_benar(): void
    {
        $response = $this->get('/demo/barang?page=7');

        $response->assertStatus(200);
        $response->assertSee('Menampilkan 43-47 dari 47 barang');
    }

    /**
     * d. Halaman memuat teks "Ubah Data" dan "Hapus Data".
     */
    public function test_tombol_aksi_berlabel_ubah_data_dan_hapus_data(): void
    {
        $response = $this->get('/demo/barang');

        $response->assertStatus(200);
        $response->assertSee('Ubah Data');
        $response->assertSee('Hapus Data');
    }

    /**
     * e. Halaman tidak memuat tag "<svg".
     */
    public function test_halaman_tidak_mengandung_tag_svg(): void
    {
        $response = $this->get('/demo/barang');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }
}

