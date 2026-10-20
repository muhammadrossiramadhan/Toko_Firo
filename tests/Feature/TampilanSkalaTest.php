<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TampilanSkalaTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Memastikan ketiga rute (admin.barang, admin.laporan.penjualan, demo.barang)
     * memuat skala tampilan 80% dan tidak memuat font-size 16px.
     */
    public function test_skala_tampilan_semua_halaman(): void
    {
        $admin = Pengguna::factory()->create([
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $routes = [
            route('demo.barang'),
            route('admin.barang'),
            route('admin.laporan.penjualan'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($admin)->get($url);

            $response->assertStatus(200);
            $response->assertSee('id="skala-tampilan"', false);
            $response->assertSee('--skala', false);
            $response->assertDontSee('font-size: 16px', false);
            $response->assertDontSee('font-size:16px', false);
        }
    }
}

