<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminAksesKasirTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $admin;
    protected Pengguna $kasir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Pengguna::factory()->create([
            'nama' => 'Admin Pemilik Toko',
            'username' => 'admin_pemilik_' . time(),
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $this->kasir = Pengguna::factory()->kasir()->create([
            'status' => 'AKTIF',
        ]);
    }

    /**
     * a. User role admin berhasil mengakses GET /kasir/transaksi (HTTP 200).
     */
    public function test_admin_berhasil_mengakses_kasir_transaksi(): void
    {
        $response = $this->actingAs($this->admin)->get('/kasir/transaksi');

        $response->assertStatus(200);
        $response->assertSee('Transaksi Penjualan');
    }

    /**
     * b. User role admin berhasil mengakses GET /kasir/stok (HTTP 200).
     */
    public function test_admin_berhasil_mengakses_kasir_stok(): void
    {
        $response = $this->actingAs($this->admin)->get('/kasir/stok');

        $response->assertStatus(200);
        $response->assertSee('Stok');
    }

    /**
     * c. User role admin berhasil mengakses GET /kasir/saldo (HTTP 200).
     */
    public function test_admin_berhasil_mengakses_kasir_saldo(): void
    {
        $response = $this->actingAs($this->admin)->get('/kasir/saldo');

        $response->assertStatus(200);
        $response->assertSee('Saldo');
    }

    /**
     * d. User role admin berhasil mengakses GET /kasir/info-barang (HTTP 200).
     */
    public function test_admin_berhasil_mengakses_kasir_info_barang(): void
    {
        $response = $this->actingAs($this->admin)->get('/kasir/info-barang');

        $response->assertStatus(200);
        $response->assertSee('Info Barang Baru');
    }

    /**
     * e. User role kasir tetap memiliki akses normal (HTTP 200).
     */
    public function test_kasir_tetap_memiliki_akses_normal_ke_semua_modul(): void
    {
        $this->actingAs($this->kasir)->get('/kasir/dashboard')->assertStatus(200);
        $this->actingAs($this->kasir)->get('/kasir/transaksi')->assertStatus(200);
        $this->actingAs($this->kasir)->get('/kasir/stok')->assertStatus(200);
        $this->actingAs($this->kasir)->get('/kasir/saldo')->assertStatus(200);
        $this->actingAs($this->kasir)->get('/kasir/info-barang')->assertStatus(200);
    }

    /**
     * f. Pengguna tanpa login (guest) tetap diarahkan ke login (HTTP 302).
     */
    public function test_pengguna_tanpa_login_diarahkan_ke_login(): void
    {
        $endpoints = [
            '/kasir/dashboard',
            '/kasir/transaksi',
            '/kasir/stok',
            '/kasir/saldo',
            '/kasir/info-barang',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->get($endpoint);

            $response->assertStatus(302);
            $this->assertTrue(
                $response->isRedirect(url('/masuk')) ||
                $response->isRedirect(url('/login')) ||
                $response->isRedirect(route('login'))
            );
        }
    }
}

