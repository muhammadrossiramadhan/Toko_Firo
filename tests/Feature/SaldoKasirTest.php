<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SaldoKasirTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Endpoint GET /kasir/saldo mengembalikan status 200 dan memuat konten utama Figma P08.
     */
    public function test_halaman_saldo_kasir_dapat_diakses_dan_memuat_konten(): void
    {
        $response = $this->get(route('kasir.saldo'));

        $response->assertStatus(200);
        $response->assertSee('Saldo');
        $response->assertSee('Saldo Hari Ini');
        $response->assertSee('Simpan sebagai PDF');
        $response->assertSee('Kirim ke Telegram');
        $response->assertSee('Modal Awal');
        $response->assertSee('Riwayat Transaksi');
    }

    /**
     * Halaman Saldo Kasir tidak boleh memuat tag "<svg" apa pun (Kepatuhan K12).
     */
    public function test_halaman_saldo_tidak_memuat_svg(): void
    {
        $response = $this->get(route('kasir.saldo'));

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Halaman Saldo Kasir memuat partial skala tampilan 80% (Kepatuhan K18).
     */
    public function test_halaman_saldo_memuat_partial_skala(): void
    {
        $response = $this->get(route('kasir.saldo'));

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
    }

    /**
     * Sidebar kasir menandai menu Saldo sebagai aktif.
     */
    public function test_menu_saldo_aktif(): void
    {
        $response = $this->get(route('kasir.saldo'));

        $response->assertStatus(200);
        $response->assertSee('bg-emerald-600 text-white font-semibold shadow-sm');
    }

    /**
     * Kontrol interaktif pada saldo kasir memiliki touch-target minimal 44px (Kepatuhan K18).
     */
    public function test_touch_target_elemen_interaktif(): void
    {
        $response = $this->get(route('kasir.saldo'));

        $response->assertStatus(200);
        $response->assertSee('min-h-[2.75rem]');
    }
}

