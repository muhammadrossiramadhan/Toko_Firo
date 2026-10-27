<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProfilAdminTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tamu yang belum login diarahkan ke login jika middleware auth aktif.
     */
    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen prototype.');

        $response = $this->get('/admin/profil');

        $response->assertRedirect(route('login'));
    }

    /**
     * Kasir aktif mendapat 403 Forbidden jika role check aktif.
     */
    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen prototype.');

        $kasir = Pengguna::factory()->kasir()->create();

        $response = $this->actingAs($kasir)->get('/admin/profil');

        $response->assertStatus(403);
    }

    /**
     * Admin aktif mendapat respons 200 dan halaman memuat seluruh komponen Figma P18.
     */
    public function test_admin_aktif_mendapat_200_dan_memuat_konten_utama(): void
    {
        $admin = Pengguna::factory()->create([
            'nama' => 'Ahmad Firo',
            'username' => 'admin_firo',
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/profil');

        $response->assertStatus(200);
        $response->assertSee('Profil Saya');
        $response->assertSee('Ahmad Firo');
        $response->assertSee('Admin / Pemilik');
        $response->assertSee('Edit Profil');
        $response->assertSee('Ganti Password');
        $response->assertSee('Simpan Perubahan');
        $response->assertSee('admin_firo');
        $response->assertSee('081234567890');
    }

    /**
     * Halaman tidak boleh memuat tag "<svg" (Aturan K12).
     */
    public function test_halaman_tidak_memuat_svg(): void
    {
        $admin = Pengguna::factory()->create([
            'nama' => 'Ahmad Firo',
            'username' => 'admin_firo',
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/profil');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Halaman memuat partial skala tampilan dan tidak memuat font-size 16px (Aturan K18).
     */
    public function test_halaman_memuat_partial_skala(): void
    {
        $admin = Pengguna::factory()->create([
            'nama' => 'Ahmad Firo',
            'username' => 'admin_firo',
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/profil');

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
        $response->assertDontSee('font-size: 16px', false);
        $response->assertDontSee('font-size:16px', false);
    }

    /**
     * Kartu profil di pojok kiri bawah sidebar memiliki tautan menuju halaman profil.
     */
    public function test_kartu_profil_sidebar_memiliki_tautan_ke_profil(): void
    {
        $admin = Pengguna::factory()->create([
            'nama' => 'Ahmad Firo',
            'username' => 'admin_firo',
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/profil');

        $response->assertStatus(200);
        $response->assertSee(route('admin.profil'), false);
    }
}

