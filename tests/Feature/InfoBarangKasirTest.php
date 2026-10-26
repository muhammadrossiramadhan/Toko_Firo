<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InfoBarangKasirTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Endpoint GET /kasir/info-barang mengembalikan status 200 dan memuat konten utama Figma P09.
     */
    public function test_halaman_info_barang_kasir_dapat_diakses_dan_memuat_konten(): void
    {
        $response = $this->get(route('kasir.info-barang'));

        $response->assertStatus(200);
        $response->assertSee('Info Barang Baru');
        $response->assertSee('Semua');
        $response->assertSee('Barang Baru');
        $response->assertSee('Perubahan Harga');
        $response->assertSee('Stok Diperbarui');
    }

    /**
     * Halaman Info Barang Baru tidak boleh memuat tag "<svg" apa pun (Kepatuhan K12).
     */
    public function test_halaman_info_barang_tidak_memuat_svg(): void
    {
        $response = $this->get(route('kasir.info-barang'));

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Halaman Info Barang Baru memuat partial skala tampilan 80% (Kepatuhan K18).
     */
    public function test_halaman_info_barang_memuat_partial_skala(): void
    {
        $response = $this->get(route('kasir.info-barang'));

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
    }

    /**
     * Sidebar kasir menandai menu Info Barang Baru sebagai aktif.
     */
    public function test_menu_info_barang_aktif(): void
    {
        $response = $this->get(route('kasir.info-barang'));

        $response->assertStatus(200);
        $response->assertSee('bg-emerald-600 text-white font-semibold shadow-sm');
    }

    /**
     * Kontrol interaktif pada info barang baru memiliki touch-target minimal 44px (Kepatuhan K18).
     */
    public function test_touch_target_elemen_interaktif(): void
    {
        $response = $this->get(route('kasir.info-barang'));

        $response->assertStatus(200);
        $response->assertSee('min-h-[2.75rem]');
    }
}

