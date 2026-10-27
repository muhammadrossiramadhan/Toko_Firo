<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TransaksiKasirTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Endpoint GET /kasir/transaksi mengembalikan status 200 dan memuat konten utama Figma P06.
     */
    public function test_halaman_transaksi_kasir_dapat_diakses_dan_memuat_konten(): void
    {
        $response = $this->get(route('kasir.transaksi'));

        $response->assertStatus(200);
        $response->assertSee('Transaksi Penjualan');
        $response->assertSee('Pilih Barang');
        $response->assertSee('Keranjang');
        $response->assertSee('Lanjutkan -&gt;', false);
        $response->assertSee('Cetak Struk');
    }

    /**
     * Halaman Transaksi Kasir tidak boleh memuat tag "<svg" apa pun (Kepatuhan K12).
     */
    public function test_halaman_transaksi_tidak_memuat_svg(): void
    {
        $response = $this->get(route('kasir.transaksi'));

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Halaman Transaksi Kasir memuat partial skala tampilan 80% (Kepatuhan K18).
     */
    public function test_halaman_transaksi_memuat_partial_skala(): void
    {
        $response = $this->get(route('kasir.transaksi'));

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
    }

    /**
     * Sidebar kasir menandai menu Transaksi Penjualan sebagai aktif.
     */
    public function test_menu_transaksi_penjualan_aktif(): void
    {
        $response = $this->get(route('kasir.transaksi'));

        $response->assertStatus(200);
        $response->assertSee('bg-emerald-600 text-white font-semibold shadow-sm');
    }

    /**
     * Kontrol interaktif pada transaksi kasir memiliki touch-target minimal 44px (Kepatuhan K18).
     */
    public function test_touch_target_elemen_interaktif(): void
    {
        $response = $this->get(route('kasir.transaksi'));

        $response->assertStatus(200);
        $response->assertSee('min-h-[2.75rem]');
    }
}

