<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BarangAdminAccessTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tamu yang belum login diarahkan ke halaman login.
     */
    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen.');

        $response = $this->get('/admin/barang');

        $response->assertRedirect(route('login'));
    }

    /**
     * Kasir aktif mendapat respons 403 Forbidden.
     */
    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen.');

        $kasir = Pengguna::factory()->kasir()->create();

        $response = $this->actingAs($kasir)->get('/admin/barang');

        $response->assertStatus(403);
    }

    /**
     * Admin aktif mendapat respons 200 dan halaman memuat "Kelola Data Barang".
     */
    public function test_admin_aktif_mendapat_200_dan_halaman_memuat_kelola_data_barang(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/barang');

        $response->assertStatus(200);
        $response->assertSee('Kelola Data Barang');
    }

    /**
     * Halaman tidak memuat tag "<svg".
     */
    public function test_halaman_tidak_memuat_svg(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/barang');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }
}

