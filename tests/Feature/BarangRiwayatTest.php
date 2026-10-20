<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BarangRiwayatTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Endpoint GET /admin/barang/riwayat mengembalikan HTTP 200 dan memuat data riwayat transaksi.
     */
    public function test_admin_melihat_halaman_riwayat_transaksi(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/barang/riwayat');

        $response->assertStatus(200);
        $response->assertSee('Riwayat Transaksi');
        $response->assertSee('TRX-001');
        $response->assertSee('TRX-008');
        $response->assertSee('Kembali ke Data Barang');
        $response->assertSee('Ekspor Data');
        $response->assertSee('Rentang: 1 Okt — 3 Okt 2026');
    }

    /**
     * Halaman riwayat transaksi tidak boleh memuat tag "<svg".
     */
    public function test_halaman_riwayat_tidak_memuat_svg(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/barang/riwayat');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }

    /**
     * Halaman riwayat transaksi memuat partial skala tampilan 80% dan tidak memuat font-size 16px kaku.
     */
    public function test_halaman_riwayat_memuat_partial_skala(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/barang/riwayat');

        $response->assertStatus(200);
        $response->assertSee('--skala', false);
        $response->assertDontSee('font-size: 16px', false);
        $response->assertDontSee('font-size:16px', false);
    }

    /**
     * Halaman utama kelola data barang memuat tautan menuju riwayat transaksi.
     */
    public function test_halaman_barang_memuat_tautan_riwayat_transaksi(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/barang');

        $response->assertStatus(200);
        $response->assertSee(route('admin.barang.riwayat'));
        $response->assertSee('Riwayat Transaksi');
    }
}

