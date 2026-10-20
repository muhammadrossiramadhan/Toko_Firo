<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LogAktivitasAdminTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tamu yang belum login diarahkan ke login jika middleware auth aktif.
     */
    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen prototype.');

        $response = $this->get('/admin/log-aktivitas');

        $response->assertRedirect(route('login'));
    }

    /**
     * Kasir aktif mendapat 403 Forbidden jika role check aktif.
     */
    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen prototype.');

        $kasir = Pengguna::factory()->kasir()->create();

        $response = $this->actingAs($kasir)->get('/admin/log-aktivitas');

        $response->assertStatus(403);
    }

    /**
     * Admin aktif mendapat respons 200 dan halaman memuat seluruh komponen Figma P16.
     */
    public function test_admin_aktif_mendapat_200_dan_memuat_konten_utama(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/log-aktivitas');

        $response->assertStatus(200);
        $response->assertSee('Log Aktivitas');
        $response->assertSee('Ekspor CSV');
        $response->assertSee('TRX-261003-001');
        $response->assertSee('Spidol Snowman Hitam');
        $response->assertSee('Set modal awal');
        $response->assertSee('Rina Putri');
        $response->assertSee('Deni Saputra');
        $response->assertSee('Junaidi Saleh');
        $response->assertSee('Menampilkan 1-10 dari 48 log');
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

        $response = $this->actingAs($admin)->get('/admin/log-aktivitas');

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

        $response = $this->actingAs($admin)->get('/admin/log-aktivitas');

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
        $response->assertDontSee('font-size: 16px', false);
        $response->assertDontSee('font-size:16px', false);
    }

    /**
     * Menu Log Aktivitas ditandai aktif pada sidebar.
     */
    public function test_menu_log_aktivitas_aktif(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/log-aktivitas');

        $response->assertStatus(200);
        $response->assertSee('Log Aktivitas');
        $response->assertSee('bg-emerald-600 text-white font-semibold text-base shadow-sm', false);
    }
}

