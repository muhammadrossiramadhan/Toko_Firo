<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaporanPembelianTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Tamu diarahkan ke halaman login.
     */
    public function test_tamu_diarahkan_ke_login(): void
    {
        $response = $this->get('/admin/laporan-pembelian');
        $response->assertRedirect(route('login'));
    }

    /**
     * Kasir aktif mendapat respons 403 Forbidden.
     */
    public function test_kasir_aktif_mendapat_403(): void
    {
        $kasir = Pengguna::factory()->kasir()->create();
        $response = $this->actingAs($kasir)->get('/admin/laporan-pembelian');
        $response->assertStatus(403);
    }

    /**
     * Admin aktif mendapat respons 200 dan memuat konten utama Laporan Pembelian.
     */
    public function test_admin_aktif_mendapat_200_dan_memuat_konten_utama(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/laporan-pembelian');

        $response->assertStatus(200);
        $response->assertSee('Laporan Pembelian');
        $response->assertSee('Total Pembelian');
        $response->assertSee('Jumlah Nota');
        $response->assertSee('Barang Masuk');
        $response->assertSee('Riwayat Transaksi');
        $response->assertSee(route('admin.laporan.penjualan'));
        $response->assertDontSee('<svg', false);
    }

    /**
     * Filter rentang tanggal pada laporan pembelian berfungsi.
     */
    public function test_filter_rentang_laporan_pembelian(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/laporan-pembelian?rentang=7-hari');
        $response->assertStatus(200);

        $responseBulan = $this->actingAs($admin)->get('/admin/laporan-pembelian?rentang=bulan-ini');
        $responseBulan->assertStatus(200);
    }

    /**
     * Tab pembelian pada halaman laporan penjualan mengarah ke rute laporan pembelian.
     */
    public function test_tab_pembelian_pada_laporan_penjualan_dapat_diklik(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan');
        $response->assertStatus(200);
        $response->assertSee(route('admin.laporan.pembelian'));
    }
}

