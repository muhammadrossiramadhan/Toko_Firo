<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardKasirTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Endpoint GET /kasir/dashboard mengembalikan status 200 dan memuat konten utama Figma P05.
     */
    public function test_halaman_dashboard_kasir_dapat_diakses_dan_memuat_konten(): void
    {
        $response = $this->get(route('kasir.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Halo, Rina');
        $response->assertSee('Rp 3.180.000');
        $response->assertSee('+ Transaksi Baru');
        $response->assertSee('Kertas HVS A4 70gr');
        $response->assertSee('Spidol Snowman Hitam ditambahkan');
    }

    /**
     * Halaman Dashboard Kasir tidak boleh memuat tag "<svg" apa pun (Kepatuhan K12).
     */
    public function test_halaman_kasir_tidak_memuat_svg(): void
    {
        $response = $this->get(route('kasir.dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Halaman Dashboard Kasir memuat partial skala tampilan 80% (Kepatuhan K18).
     */
    public function test_halaman_kasir_memuat_partial_skala(): void
    {
        $response = $this->get(route('kasir.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
    }

    /**
     * Sidebar kasir menandai menu Dashboard sebagai aktif dengan warna emerald.
     */
    public function test_menu_dashboard_kasir_aktif(): void
    {
        $response = $this->get(route('kasir.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('bg-emerald-600 text-white font-semibold');
    }

    /**
     * Kontrol interaktif pada dashboard kasir memiliki touch-target minimal 44px (Kepatuhan K18).
     */
    public function test_touch_target_elemen_interaktif(): void
    {
        $response = $this->get(route('kasir.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('min-h-[2.75rem]');
    }
}

