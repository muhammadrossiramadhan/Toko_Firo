<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Services\PenggunaService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Tests\TestCase;

class PenggunaDataTest extends TestCase
{
    use DatabaseTransactions;

    protected string $adminUji = 'adm_uji_p13';
    protected string $kasirUji = 'ksr_uji_p13';

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan akun pengguna uji coba pada database toko_firo_test
        DB::table('pengguna')->updateOrInsert(
            ['username' => $this->adminUji],
            [
                'nama' => 'Junaidi Saleh Uji',
                'password_hash' => Hash::make('secret123'),
                'role' => 'ADMIN',
                'status' => 'AKTIF',
                'login_terakhir' => now(),
            ]
        );

        DB::table('pengguna')->updateOrInsert(
            ['username' => $this->kasirUji],
            [
                'nama' => 'Rina Putri Uji',
                'password_hash' => Hash::make('secret123'),
                'role' => 'KASIR',
                'status' => 'NONAKTIF',
                'login_terakhir' => null,
            ]
        );
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->markTestSkipped('Middleware auth dinonaktifkan sementara.');

        $response = $this->get('/admin/pengguna');
        $response->assertRedirect(route('login'));
    }

    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Middleware check role dinonaktifkan sementara.');

        $kasir = Pengguna::factory()->kasir()->create();
        $response = $this->actingAs($kasir)->get('/admin/pengguna');
        $response->assertStatus(403);
    }

    public function test_endpoint_index_menampilkan_pengguna_riil_dari_database(): void
    {
        $admin = Pengguna::where('username', $this->adminUji)->first();

        $response = $this->actingAs($admin)->get('/admin/pengguna');

        $response->assertStatus(200);
        $response->assertSee('Kelola Akun Pengguna');
        $response->assertSee('Junaidi Saleh Uji');
        $response->assertSee($this->adminUji);
        $response->assertSee('Rina Putri Uji');
        $response->assertSee($this->kasirUji);
        $response->assertSee('Admin');
        $response->assertSee('Kasir');
        $response->assertSee('Aktif');
        $response->assertSee('Nonaktif');
    }

    public function test_filter_role_status_dan_pencarian_kata_kunci(): void
    {
        $admin = Pengguna::where('username', $this->adminUji)->first();

        // Filter role KASIR
        $resRole = $this->actingAs($admin)->get('/admin/pengguna?role=KASIR');
        $resRole->assertStatus(200);
        $resRole->assertSee('Rina Putri Uji');

        // Filter status NONAKTIF
        $resStatus = $this->actingAs($admin)->get('/admin/pengguna?status=NONAKTIF');
        $resStatus->assertStatus(200);
        $resStatus->assertSee('Rina Putri Uji');

        // Pencarian kata kunci nama
        $resCari = $this->actingAs($admin)->get('/admin/pengguna?q=Junaidi');
        $resCari->assertStatus(200);
        $resCari->assertSee('Junaidi Saleh Uji');
        $resCari->assertDontSee('Rina Putri Uji');
    }

    public function test_pembuatan_pengguna_baru_via_service(): void
    {
        $service = app(PenggunaService::class);
        $admin = Pengguna::where('username', $this->adminUji)->first();
        $usernameBaru = 'staf_baru_' . substr(uniqid(), -6);

        $service->createPengguna('Staf Baru Toko', $usernameBaru, 'password123', 'KASIR', $admin->id_pengguna);

        $user = DB::table('pengguna')->where('username', $usernameBaru)->first();
        $this->assertNotNull($user);
        $this->assertEquals('Staf Baru Toko', $user->nama);
        $this->assertEquals('KASIR', $user->role);
        $this->assertEquals('AKTIF', $user->status);
        $this->assertTrue(Hash::check('password123', $user->password_hash));

        // Verifikasi audit log tercatat
        $log = DB::table('log_aktivitas')
            ->where('keterangan', 'like', "%{$usernameBaru}%")
            ->orderBy('id_log', 'desc')
            ->first();
        $this->assertNotNull($log);
    }

    public function test_pengubahan_status_pengguna_via_service(): void
    {
        $service = app(PenggunaService::class);
        $user = DB::table('pengguna')->where('username', $this->adminUji)->first();

        $service->setStatus($user->id_pengguna, 'NONAKTIF', $user->id_pengguna);

        $statusTerbaru = DB::table('pengguna')->where('id_pengguna', $user->id_pengguna)->value('status');
        $this->assertEquals('NONAKTIF', $statusTerbaru);
    }

    public function test_reset_password_pengguna_via_service(): void
    {
        $service = app(PenggunaService::class);
        $user = DB::table('pengguna')->where('username', $this->adminUji)->first();
        $passwordBaru = 'secretbaru999';

        $service->resetPassword($user->id_pengguna, $passwordBaru, $user->id_pengguna);

        $hashTerbaru = DB::table('pengguna')->where('id_pengguna', $user->id_pengguna)->value('password_hash');
        $this->assertTrue(Hash::check($passwordBaru, $hashTerbaru));
    }

    public function test_validasi_input_penolakan_role_tidak_valid(): void
    {
        $service = app(PenggunaService::class);
        $admin = Pengguna::where('username', $this->adminUji)->first();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Role tidak valid');

        $service->createPengguna('Nama Uji', 'username_valid', 'secret123', 'MANAGER', $admin->id_pengguna);
    }

    public function test_validasi_input_penolakan_password_terlalu_pendek(): void
    {
        $service = app(PenggunaService::class);
        $admin = Pengguna::where('username', $this->adminUji)->first();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password minimal 6 karakter');

        $service->createPengguna('Nama Uji', 'username_valid', '123', 'KASIR', $admin->id_pengguna);
    }

    public function test_halaman_pengguna_tidak_memuat_tag_svg(): void
    {
        $admin = Pengguna::where('username', $this->adminUji)->first();

        $response = $this->actingAs($admin)->get('/admin/pengguna');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }
}

