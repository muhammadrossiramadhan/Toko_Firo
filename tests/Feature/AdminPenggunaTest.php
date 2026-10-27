<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPenggunaTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $admin;
    protected string $adminUsername = 'adm_test_feat';

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('pengguna')->updateOrInsert(
            ['username' => $this->adminUsername],
            [
                'nama' => 'Admin Testing Feature',
                'password_hash' => Hash::make('password123'),
                'role' => 'ADMIN',
                'status' => 'AKTIF',
                'login_terakhir' => now(),
            ]
        );

        $this->admin = Pengguna::where('username', $this->adminUsername)->first();
    }

    public function test_admin_dapat_mengakses_halaman_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pengguna.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Akun Pengguna');
        $response->assertSee('Admin Testing Feature');
        $response->assertDontSee('<svg', false);
    }

    public function test_admin_dapat_menambah_akun_pengguna_baru(): void
    {
        $username = 'kasir_baru_' . substr(uniqid(), -6);

        $response = $this->actingAs($this->admin)->post(route('admin.pengguna.store'), [
            'nama' => 'Kasir Baru Toko',
            'username' => $username,
            'password' => 'secret123',
            'peran' => 'kasir',
        ]);

        $response->assertRedirect(route('admin.pengguna.index'));
        $this->assertDatabaseHas('pengguna', [
            'username' => $username,
            'nama' => 'Kasir Baru Toko',
            'role' => 'KASIR',
            'status' => 'AKTIF',
        ]);

        $user = DB::table('pengguna')->where('username', $username)->first();
        $this->assertTrue(Hash::check('secret123', $user->password_hash));
    }

    public function test_admin_dapat_mengubah_data_pengguna(): void
    {
        $username = 'kasir_edit_' . substr(uniqid(), -6);
        $id = DB::table('pengguna')->insertGetId([
            'nama' => 'Kasir Awal',
            'username' => $username,
            'password_hash' => Hash::make('password123'),
            'role' => 'KASIR',
            'status' => 'AKTIF',
            'dibuat_pada' => now(),
            'diubah_pada' => now(),
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.pengguna.update', $id), [
            'nama' => 'Kasir Diedit',
            'username' => $username . '_mod',
            'peran' => 'kasir',
        ]);

        $response->assertRedirect(route('admin.pengguna.index'));
        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $id,
            'nama' => 'Kasir Diedit',
            'username' => $username . '_mod',
        ]);
    }

    public function test_admin_dapat_mengubah_status_aktif_nonaktif(): void
    {
        $username = 'kasir_stat_' . substr(uniqid(), -6);
        $id = DB::table('pengguna')->insertGetId([
            'nama' => 'Kasir Status',
            'username' => $username,
            'password_hash' => Hash::make('password123'),
            'role' => 'KASIR',
            'status' => 'AKTIF',
            'dibuat_pada' => now(),
            'diubah_pada' => now(),
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.pengguna.status', $id), [
            'status' => 'NONAKTIF',
        ]);

        $response->assertRedirect(route('admin.pengguna.index'));
        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $id,
            'status' => 'NONAKTIF',
        ]);
    }

    public function test_admin_dapat_mereset_password(): void
    {
        $username = 'kasir_rst_' . substr(uniqid(), -6);
        $id = DB::table('pengguna')->insertGetId([
            'nama' => 'Kasir Reset',
            'username' => $username,
            'password_hash' => Hash::make('old_secret123'),
            'role' => 'KASIR',
            'status' => 'AKTIF',
            'dibuat_pada' => now(),
            'diubah_pada' => now(),
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.pengguna.reset-password', $id), [
            'password_baru' => 'new_secret123',
        ]);

        $response->assertRedirect(route('admin.pengguna.index'));
        $user = DB::table('pengguna')->where('id_pengguna', $id)->first();
        $this->assertTrue(Hash::check('new_secret123', $user->password_hash));
    }

    public function test_admin_tidak_dapat_menonaktifkan_akun_sendiri(): void
    {
        $response = $this->actingAs($this->admin)->patch(route('admin.pengguna.status', $this->admin->id_pengguna), [
            'status' => 'NONAKTIF',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $this->admin->id_pengguna,
            'status' => 'AKTIF',
        ]);
    }

    public function test_admin_tidak_dapat_menghapus_akun_sendiri(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.pengguna.destroy', $this->admin->id_pengguna));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $this->admin->id_pengguna,
        ]);
    }

    public function test_admin_dapat_menghapus_akun_pengguna_lain(): void
    {
        $username = 'kasir_del_' . substr(uniqid(), -6);
        $id = DB::table('pengguna')->insertGetId([
            'nama' => 'Kasir Dihapus',
            'username' => $username,
            'password_hash' => Hash::make('secret123'),
            'role' => 'KASIR',
            'status' => 'AKTIF',
            'dibuat_pada' => now(),
            'diubah_pada' => now(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.pengguna.destroy', $id));

        $response->assertRedirect(route('admin.pengguna.index'));
        $this->assertDatabaseMissing('pengguna', [
            'id_pengguna' => $id,
        ]);
    }
}

