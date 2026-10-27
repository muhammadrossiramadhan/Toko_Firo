<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StokKasirTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $kasir = Pengguna::factory()->kasir()->create();
        $this->actingAs($kasir);
    }

    /**
     * Endpoint GET /kasir/stok mengembalikan status 200 dan memuat konten utama Figma P07.
     */
    public function test_halaman_stok_kasir_dapat_diakses_dan_memuat_konten(): void
    {
        $response = $this->get(route('kasir.stok'));

        $response->assertStatus(200);
        $response->assertSee('Lihat Stok Barang');
        $response->assertSee('Cari nama atau kode barang...');
        $response->assertSee('Pensil 2B Faber');
        $response->assertSee('Kertas HVS A4 70gr');
        $response->assertSee('Habis');
        $response->assertSee('Menipis');
    }

    /**
     * Halaman Stok Kasir tidak boleh memuat tag "<svg" apa pun (Kepatuhan K12).
     */
    public function test_halaman_stok_tidak_memuat_svg(): void
    {
        $response = $this->get(route('kasir.stok'));

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Halaman Stok Kasir memuat partial skala tampilan 80% (Kepatuhan K18).
     */
    public function test_halaman_stok_memuat_partial_skala(): void
    {
        $response = $this->get(route('kasir.stok'));

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
    }

    /**
     * Sidebar kasir menandai menu Lihat Stok Barang sebagai aktif.
     */
    public function test_menu_stok_barang_aktif(): void
    {
        $response = $this->get(route('kasir.stok'));

        $response->assertStatus(200);
        $response->assertSee('bg-emerald-600 text-white font-semibold shadow-sm');
    }

    /**
     * Kontrol interaktif pada stok kasir memiliki touch-target minimal 44px (Kepatuhan K18).
     */
    public function test_touch_target_elemen_interaktif(): void
    {
        $response = $this->get(route('kasir.stok'));

        $response->assertStatus(200);
        $response->assertSee('min-h-[2.75rem]');
    }
}

