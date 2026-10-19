<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaporanPenjualanAccessTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tamu yang belum login diarahkan ke halaman login.
     */
    public function test_tamu_diarahkan_ke_login(): void
    {
        $response = $this->get('/admin/laporan-penjualan');

        $response->assertRedirect(route('login'));
    }

    /**
     * Kasir aktif mendapat respons 403 Forbidden.
     */
    public function test_kasir_aktif_mendapat_403(): void
    {
        $kasir = Pengguna::factory()->kasir()->create();

        $response = $this->actingAs($kasir)->get('/admin/laporan-penjualan');

        $response->assertStatus(403);
    }

    /**
     * Admin aktif mendapat respons 200 dan halaman memuat elemen penting.
     */
    public function test_admin_aktif_mendapat_200_dan_memuat_konten_utama(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan');

        $response->assertStatus(200);
        $response->assertSee('Laporan Penjualan');
        $response->assertSee('Riwayat Transaksi');
        $response->assertSee('Rekap Otomatis');
        $response->assertSee('Simpan sebagai PDF');
        $response->assertSee('Kirim ke Telegram');
        $response->assertSee('Total Penjualan');
        $response->assertSee('Jumlah Nota');
        $response->assertSee('Barang Terjual');
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

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Parameter rentang=7-hari mengisi nilai tanggal yang benar di form.
     */
    public function test_rentang_tujuh_hari_mengisi_nilai_tanggal_yang_benar_di_form(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan?rentang=7-hari');

        $response->assertStatus(200);
        $mulaiTujuhHari = date('Y-m-d', strtotime('-6 days'));
        $sampaiHariIni = date('Y-m-d');
        $response->assertSee('value="' . $mulaiTujuhHari . '"', false);
        $response->assertSee('value="' . $sampaiHariIni . '"', false);
    }

    /**
     * Tanggal tidak valid menampilkan pesan.
     */
    public function test_tanggal_tidak_valid_menampilkan_pesan(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan?rentang=kustom&mulai=2026-10-20&sampai=2026-10-10');

        $response->assertStatus(200);
        $response->assertSee('Tanggal tidak valid, ditampilkan hari ini.');
    }
}

