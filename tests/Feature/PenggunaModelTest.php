<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PenggunaModelTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test pembuatan pengguna lewat factory dan verifikasi getAuthPassword.
     */
    public function test_membuat_pengguna_lewat_factory_dan_get_auth_password(): void
    {
        $pengguna = Pengguna::factory()->create();

        $this->assertNotNull($pengguna->id_pengguna);
        $this->assertEquals($pengguna->password_hash, $pengguna->getAuthPassword());
        $this->assertEquals('ADMIN', $pengguna->role);
        $this->assertEquals('AKTIF', $pengguna->status);
    }

    /**
     * Test state kasir menghasilkan role KASIR dan nonaktif menghasilkan status NONAKTIF.
     */
    public function test_state_kasir_dan_nonaktif(): void
    {
        $kasir = Pengguna::factory()->kasir()->create();
        $this->assertEquals('KASIR', $kasir->role);

        $nonaktif = Pengguna::factory()->nonaktif()->create();
        $this->assertEquals('NONAKTIF', $nonaktif->status);
    }
}

