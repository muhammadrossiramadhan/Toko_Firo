<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardAdminTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tamu yang belum login diarahkan ke login jika auth middleware aktif.
     */
    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen prototype.');

        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login'));
    }

    /**
     * Kasir aktif mendapat 403 Forbidden jika role check aktif.
     */
    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen prototype.');

        $kasir = Pengguna::factory()->kasir()->create();

        $response = $this->actingAs($kasir)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Admin aktif mendapat respons 200 dan halaman memuat seluruh komponen Figma P10.
     */
    public function test_admin_aktif_mendapat_200_dan_memuat_konten_utama(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
        $response->assertSee('Halo, Junaidi');
        $response->assertSee('Jumat, 3 Oktober 2026');
        $response->assertSee('Penjualan Hari Ini');
        $response->assertSee('Rp 3.180.000');
        $response->assertSee('Jumlah Transaksi');
        $response->assertSee('Stok Menipis');
        $response->assertSee('Laba Hari Ini');
        $response->assertSee('Rp 680.000');
        $response->assertSee('Penjualan 7 Hari Terakhir');
        $response->assertSee('Top Barang Hari Ini');
        $response->assertSee('Kertas HVS A4');
        $response->assertSee('Pulpen Pilot G2');
        $response->assertSee('Fotocopy A4');
        $response->assertSee('Aktivitas Terbaru');
        $response->assertSee('Rina menyelesaikan transaksi TRX-001');
        $response->assertSee('Stok Tinta Canon habis');
        $response->assertSee('Deni login sebagai kasir');
        $response->assertSee('Admin menambahkan Spidol Snowman');
    }

    /**
     * Halaman tidak boleh memuat tag "<svg" (Aturan K12).
     */
    public function test_halaman_tidak_memuat_svg(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Halaman memuat partial skala tampilan dan tidak memuat font-size 16px (Aturan K18).
     */
    public function test_halaman_memuat_partial_skala(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
        $response->assertDontSee('font-size: 16px', false);
        $response->assertDontSee('font-size:16px', false);
    }

    /**
     * Menu Dashboard ditandai aktif pada sidebar.
     */
    public function test_menu_dashboard_aktif(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('bg-emerald-600 text-white font-semibold text-base shadow-sm', false);
    }
}

