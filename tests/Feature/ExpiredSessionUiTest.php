<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExpiredSessionUiTest extends TestCase
{
    /**
     * Uji respon HTTP 200 pada route /sesi-berakhir.
     */
    public function test_route_sesi_berakhir_mengembalikan_status_200(): void
    {
        $response = $this->get('/sesi-berakhir');

        $response->assertStatus(200);
        $response->assertSee('Sesi Telah Berakhir');
        $response->assertSee('Waktu Masuk Kedaluwarsa');
        $response->assertSee('Masuk Kembali');
        $response->assertSee('Kembali ke Beranda');
    }

    /**
     * Verifikasi output HTML bebas dari tag <svg> (Aturan K12).
     */
    public function test_halaman_sesi_berakhir_bebas_dari_tag_svg(): void
    {
        $response = $this->get('/sesi-berakhir');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Verifikasi elemen interaktif tombol login memiliki touch-target minimal 44px dan font minimal 16px (Aturan K18).
     */
    public function test_tombol_memiliki_touch_target_minimal_44px_dan_aturan_k18(): void
    {
        $response = $this->get('/sesi-berakhir');

        $response->assertStatus(200);
        $response->assertSee('touch-target');
        $response->assertSee('min-height: 44px');
        $response->assertSee('--skala');
    }

    /**
     * Verifikasi halaman error 419 memuat antarmuka sesi kedaluwarsa yang sama.
     */
    public function test_view_error_419_memuat_antarmuka_sesi_berakhir(): void
    {
        $view = $this->view('errors.419');

        $view->assertSee('Sesi Telah Berakhir');
        $view->assertSee('Masuk Kembali');
        $view->assertDontSee('<svg', false);
    }
}

