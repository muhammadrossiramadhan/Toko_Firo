<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotifikasiAdminTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tamu yang belum login diarahkan ke login jika middleware auth aktif.
     */
    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen prototype.');

        $response = $this->get('/admin/pengaturan-notifikasi');

        $response->assertRedirect(route('login'));
    }

    /**
     * Kasir aktif mendapat 403 Forbidden jika role check aktif.
     */
    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Auth dinonaktifkan sementara untuk pengujian independen prototype.');

        $kasir = Pengguna::factory()->kasir()->create();

        $response = $this->actingAs($kasir)->get('/admin/pengaturan-notifikasi');

        $response->assertStatus(403);
    }

    /**
     * Admin aktif mendapat respons 200 dan halaman memuat seluruh komponen Figma P17.
     */
    public function test_admin_aktif_mendapat_200_dan_memuat_konten_utama(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/pengaturan-notifikasi');

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Notifikasi Bot');
        $response->assertSee('Telegram Bot');
        $response->assertSee('Kirim Pesan Tes');
        $response->assertSee('WhatsApp Bot');
        $response->assertSee('Riwayat Notifikasi Terkirim');
        $response->assertSee('03 Okt 2026, 21:00');
        $response->assertSee('Terkirim');
        $response->assertSee('Gagal');
        $response->assertSee('Rekap Laporan Penjualan Harian');
        $response->assertSee('Peringatan Stok Minimum & Habis');
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

        $response = $this->actingAs($admin)->get('/admin/pengaturan-notifikasi');

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

        $response = $this->actingAs($admin)->get('/admin/pengaturan-notifikasi');

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
        $response->assertDontSee('font-size: 16px', false);
        $response->assertDontSee('font-size:16px', false);
    }

    /**
     * Menu Pengaturan Notifikasi ditandai aktif pada sidebar.
     */
    public function test_menu_pengaturan_notifikasi_aktif(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/pengaturan-notifikasi');

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Notifikasi');
        $response->assertSee('bg-emerald-600 text-white font-semibold text-base shadow-sm', false);
    }
}

