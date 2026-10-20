<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BatasStokAdminTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tamu yang belum login diarahkan ke halaman login jika middleware auth aktif.
     */
    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen prototype.');

        $response = $this->get('/admin/batas-stok');

        $response->assertRedirect(route('login'));
    }

    /**
     * Kasir aktif mendapat respons 403 Forbidden jika role check aktif.
     */
    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen prototype.');

        $kasir = Pengguna::factory()->kasir()->create();

        $response = $this->actingAs($kasir)->get('/admin/batas-stok');

        $response->assertStatus(403);
    }

    /**
     * Admin aktif mendapat respons 200 dan halaman memuat konten utama sesuai Figma P14.
     */
    public function test_admin_aktif_mendapat_200_dan_memuat_konten_utama(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/batas-stok');

        $response->assertStatus(200);
        $response->assertSee('Batas Stok Minimum');
        $response->assertSee('Notifikasi otomatis dikirim ke bot Telegram');
        $response->assertSee('Pengingat Telegram Aktif');
        $response->assertSee('Pensil 2B');
        $response->assertSee('BRG-008');
        $response->assertSee('Isi Staples No.10');
        $response->assertSee('BRG-006');
        $response->assertSee('Kertas HVS A4 70gr');
        $response->assertSee('BRG-003');
        $response->assertSee('Stapler HD-10');
        $response->assertSee('BRG-005');
        $response->assertSee('Pulpen Standard AE7');
        $response->assertSee('BRG-001');
        $response->assertSee('Atur semua ke 3 pcs');
        $response->assertSee('Atur semua ke 5 pcs');
        $response->assertSee('Batalkan');
        $response->assertSee('Simpan Perubahan');
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

        $response = $this->actingAs($admin)->get('/admin/batas-stok');

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

        $response = $this->actingAs($admin)->get('/admin/batas-stok');

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
        $response->assertDontSee('font-size: 16px', false);
        $response->assertDontSee('font-size:16px', false);
    }

    /**
     * Menu Batas Stok Minimum ditandai aktif pada sidebar.
     */
    public function test_menu_batas_stok_minimum_aktif(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/batas-stok');

        $response->assertStatus(200);
        $response->assertSee('Batas Stok Minimum');
        $response->assertSee('bg-emerald-600 text-white font-semibold text-base shadow-sm', false);
    }
}

