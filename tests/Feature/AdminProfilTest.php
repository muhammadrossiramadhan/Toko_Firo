<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfilTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Pengguna::factory()->create([
            'nama' => 'Admin Utama Toko',
            'username' => 'admin_utama',
            'password_hash' => Hash::make('password123'),
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);
    }

    /**
     * a. Admin dapat melihat halaman profil dengan data akun sendiri.
     */
    public function test_admin_dapat_melihat_halaman_profil_dengan_data_akun_sendiri(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/profil');

        $response->assertStatus(200);
        $response->assertSee('Profil Saya');
        $response->assertSee('Admin Utama Toko');
        $response->assertSee('admin_utama');
        $response->assertSee('Edit Profil');
        $response->assertSee('Ganti Password');
        $response->assertSee('Simpan Perubahan');
        $response->assertDontSee('<svg', false);
    }

    /**
     * b. Admin dapat memperbarui nama dan username secara valid di database.
     */
    public function test_admin_dapat_memperbarui_nama_dan_username_secara_valid(): void
    {
        $payload = [
            'nama' => 'Ahmad Firo Perubahan',
            'username' => 'firo_update_' . time(),
            'nomor_telepon' => '081298765432',
        ];

        $response = $this->actingAs($this->admin)->put('/admin/profil', $payload);

        $response->assertRedirect('/admin/profil');
        $response->assertSessionHas('success', 'Profil berhasil diperbarui.');

        // Verifikasi langsung ke database
        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $this->admin->id_pengguna,
            'nama' => 'Ahmad Firo Perubahan',
            'username' => $payload['username'],
        ]);
    }

    /**
     * b. (Variant) Admin dapat memperbarui profil via AJAX / JSON request.
     */
    public function test_admin_dapat_memperbarui_profil_via_json(): void
    {
        $payload = [
            'nama' => 'Admin AJAX Mode',
            'username' => 'admin_ajax_' . time(),
            'nomor_telepon' => '089988776655',
        ];

        $response = $this->actingAs($this->admin)->putJson('/admin/profil', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'data' => [
                'nama' => 'Admin AJAX Mode',
                'username' => $payload['username'],
            ],
        ]);
    }

    /**
     * c. Admin dapat memperbarui password setelah password lama terverifikasi.
     */
    public function test_admin_dapat_memperbarui_password_setelah_password_lama_terverifikasi(): void
    {
        $payload = [
            'password_lama' => 'password123',
            'password_baru' => 'newPassword456',
            'password_baru_confirmation' => 'newPassword456',
        ];

        $response = $this->actingAs($this->admin)->put('/admin/profil/password', $payload);

        $response->assertRedirect('/admin/profil');
        $response->assertSessionHas('success', 'Password berhasil diperbarui.');

        // Verifikasi password baru cocok dan password lama tidak lagi valid
        $updatedUser = Pengguna::find($this->admin->id_pengguna);
        $this->assertTrue(Hash::check('newPassword456', $updatedUser->password_hash));
        $this->assertFalse(Hash::check('password123', $updatedUser->password_hash));
    }

    /**
     * c. (Negative) Validasi menolak perubahan password jika password lama tidak sesuai.
     */
    public function test_validasi_menolak_jika_password_lama_salah(): void
    {
        $payload = [
            'password_lama' => 'passwordSalahTotal',
            'password_baru' => 'newPassword456',
            'password_baru_confirmation' => 'newPassword456',
        ];

        $response = $this->actingAs($this->admin)->put('/admin/profil/password', $payload);

        $response->assertSessionHasErrors(['password_lama']);

        // Pastikan password di database tidak berubah
        $freshUser = Pengguna::find($this->admin->id_pengguna);
        $this->assertTrue(Hash::check('password123', $freshUser->password_hash));
    }

    /**
     * d. Validasi menolak username duplikat milik pengguna lain.
     */
    public function test_validasi_menolak_username_duplikat_milik_pengguna_lain(): void
    {
        $penggunaLain = Pengguna::factory()->create([
            'username' => 'user_unik_lain',
        ]);

        $payload = [
            'nama' => 'Coba Duplikat Username',
            'username' => 'user_unik_lain',
        ];

        $response = $this->actingAs($this->admin)->put('/admin/profil', $payload);

        $response->assertSessionHasErrors(['username']);

        // Pastikan username admin tidak berubah menjadi milik orang lain
        $freshAdmin = Pengguna::find($this->admin->id_pengguna);
        $this->assertEquals('admin_utama', $freshAdmin->username);
    }
}

