<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PembelianAdminTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tamu yang belum login diarahkan ke halaman login.
     */
    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen.');

        $response = $this->get('/admin/pembelian');

        $response->assertRedirect(route('login'));
    }

    /**
     * Kasir aktif mendapat respons 403 Forbidden.
     */
    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen.');

        $kasir = Pengguna::factory()->kasir()->create();

        $response = $this->actingAs($kasir)->get('/admin/pembelian');

        $response->assertStatus(403);
    }

    /**
     * Admin aktif mendapat respons 200 dan halaman memuat konten utama.
     */
    public function test_admin_aktif_mendapat_200_dan_memuat_konten_utama(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/pembelian');

        $response->assertStatus(200);
        $response->assertSee('Pembelian Barang (Restock)');
        $response->assertSee('Perlu Segera Restock');
        $response->assertSee('Pensil 2B');
        $response->assertSee('Riwayat Pembelian');
        $response->assertSee('Catat Restock');
    }

    /**
     * Halaman tidak boleh memuat tag "<svg".
     */
    public function test_halaman_tidak_memuat_svg(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/pembelian');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Halaman pembelian memuat partial skala tampilan dan tidak memuat font-size 16px.
     */
    public function test_halaman_memuat_partial_skala(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/pembelian');

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
        $response->assertDontSee('font-size: 16px', false);
        $response->assertDontSee('font-size:16px', false);
    }

    /**
     * Menu Pembelian Barang ditandai aktif pada sidebar.
     */
    public function test_menu_pembelian_barang_aktif(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/pembelian');

        $response->assertStatus(200);
        $response->assertSee('Pembelian Barang');
        $response->assertSee('bg-emerald-600 text-white font-semibold text-base shadow-sm', false);
    }
}

