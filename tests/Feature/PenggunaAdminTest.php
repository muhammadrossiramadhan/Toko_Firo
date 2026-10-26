<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PenggunaAdminTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Endpoint GET /admin/pengguna mengembalikan HTTP 200 dan memuat konten akun pengguna.
     */
    public function test_admin_melihat_halaman_kelola_akun_pengguna(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/pengguna');

        $response->assertStatus(200);
        $response->assertSee('Kelola Akun Pengguna');
        $response->assertSee('Junaidi Saleh');
        $response->assertSee('Rina Putri');
        $response->assertSee('Siti Aminah');
        $response->assertSee('Tambah Akun');
        $response->assertSee('Menampilkan 3 pengguna terdaftar');
    }

    /**
     * Halaman kelola akun pengguna tidak boleh memuat tag "<svg".
     */
    public function test_halaman_pengguna_tidak_memuat_svg(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/pengguna');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Halaman kelola akun pengguna memuat partial skala tampilan 80% dan tidak memuat font-size 16px kaku.
     */
    public function test_halaman_pengguna_memuat_partial_skala(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/pengguna');

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
        $response->assertDontSee('font-size: 16px', false);
        $response->assertDontSee('font-size:16px', false);
    }

    /**
     * Menu sidebar Kelola Akun Pengguna berstatus aktif.
     */
    public function test_menu_sidebar_kelola_akun_pengguna_aktif(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/pengguna');

        $response->assertStatus(200);
        $response->assertSee('Kelola Akun Pengguna');
        $response->assertSee('bg-emerald-600 text-white font-semibold text-base shadow-sm', false);
    }
}

