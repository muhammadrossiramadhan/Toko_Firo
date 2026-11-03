<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\OtpService;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AuthHardeningTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * 1. Akses unauthenticated ke seluruh endpoint /admin/* dan /kasir/* wajib diarahkan ke /masuk.
     */
    public function test_akses_unauthenticated_ke_admin_dan_kasir_diarahkan_ke_masuk(): void
    {
        $endpoints = [
            '/admin/dashboard',
            '/admin/barang',
            '/admin/laporan-penjualan',
            '/admin/pengguna',
            '/admin/backup',
            '/kasir/dashboard',
            '/kasir/transaksi',
            '/kasir/stok',
            '/kasir/saldo',
            '/kasir/info-barang',
        ];

        foreach ($endpoints as $url) {
            $response = $this->get($url);
            $response->assertRedirect('/masuk');
        }
    }

    /**
     * 2a. Pengguna dengan status NONAKTIF gagal login pada tahap pertama (/masuk).
     */
    public function test_pengguna_status_nonaktif_gagal_login_tahap_pertama(): void
    {
        $unique = 'nonaktif_' . substr(uniqid(), -6);
        $user = User::create([
            'nama' => 'Staf Nonaktif',
            'username' => $unique,
            'password_hash' => Hash::make('secret123'),
            'role' => 'KASIR',
            'status' => 'NONAKTIF',
        ]);

        $mockTelegram = Mockery::mock(TelegramService::class);
        $mockTelegram->shouldNotReceive('send');
        $this->app->instance(TelegramService::class, $mockTelegram);

        $response = $this->post('/masuk', [
            'username' => $unique,
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors(['login']);
        $this->assertNull(session('pending_2fa_user_id'));
    }

    /**
     * 2b. Pengguna dengan status NONAKTIF gagal saat verifikasi OTP (/verifikasi).
     */
    public function test_pengguna_status_nonaktif_gagal_saat_verifikasi_otp(): void
    {
        $unique = 'nonaktif_otp_' . substr(uniqid(), -6);
        $user = User::create([
            'nama' => 'Staf Nonaktif OTP',
            'username' => $unique,
            'password_hash' => Hash::make('secret123'),
            'role' => 'KASIR',
            'status' => 'NONAKTIF',
        ]);

        /** @var OtpService $otpService */
        $otpService = app(OtpService::class);
        $validCode = $otpService->generate($user->id);

        $response = $this->withSession(['pending_2fa_user_id' => $user->id])
            ->post('/verifikasi', [
                'code' => $validCode,
            ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['login']);
        $this->assertFalse(Auth::check());
    }

    /**
     * 2c. Akun pengguna yang diubah menjadi NONAKTIF di tengah sesi langsung dibatalkan (logout paksa).
     */
    public function test_akun_nonaktif_di_tengah_sesi_langsung_di_logout_oleh_check_role(): void
    {
        $unique = 'mid_session_' . substr(uniqid(), -6);
        $user = User::create([
            'nama' => 'Staf Mid Session',
            'username' => $unique,
            'password_hash' => Hash::make('secret123'),
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $this->actingAs($user);
        $this->get('/admin/dashboard')->assertStatus(200);

        // Ubah status pengguna menjadi NONAKTIF di database
        DB::table('pengguna')->where('id_pengguna', $user->id)->update(['status' => 'NONAKTIF']);

        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('login'));
        $this->assertFalse(Auth::check());
    }

    /**
     * 3. Akun peran ADMIN dan PEMILIK memiliki akses penuh ke seluruh rute kasir.
     */
    public function test_akun_admin_dan_pemilik_memiliki_akses_penuh_ke_rute_kasir(): void
    {
        $admin = User::create([
            'nama' => 'Admin Utama',
            'username' => 'adm_' . substr(uniqid(), -6),
            'password_hash' => Hash::make('secret123'),
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $kasirEndpoints = [
            '/kasir/dashboard',
            '/kasir/transaksi',
            '/kasir/stok',
            '/kasir/saldo',
            '/kasir/info-barang',
        ];

        foreach ($kasirEndpoints as $url) {
            $this->actingAs($admin)->get($url)->assertStatus(200);
        }

        $pemilik = User::create([
            'nama' => 'Pemilik Toko',
            'username' => 'own_' . substr(uniqid(), -6),
            'password_hash' => Hash::make('secret123'),
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);
        $pemilik->peran = 'pemilik';

        foreach ($kasirEndpoints as $url) {
            $this->actingAs($pemilik)->get($url)->assertStatus(200);
        }
    }

    /**
     * 4. Akun KASIR mendapatkan status 403 saat mengakses rute admin.
     */
    public function test_akun_kasir_mendapatkan_status_403_saat_mengakses_rute_admin(): void
    {
        $kasir = User::create([
            'nama' => 'Kasir Staf',
            'username' => 'ksr_' . substr(uniqid(), -6),
            'password_hash' => Hash::make('secret123'),
            'role' => 'KASIR',
            'status' => 'AKTIF',
        ]);

        $adminEndpoints = [
            '/admin/dashboard',
            '/admin/barang',
            '/admin/laporan-penjualan',
            '/admin/pengguna',
            '/admin/backup',
        ];

        foreach ($adminEndpoints as $url) {
            $this->actingAs($kasir)->get($url)->assertStatus(403);
        }
    }

    /**
     * 5. Limitasi percobaan OTP bekerja dan otomatis menginfiltrasi sesi setelah melebihi batas toleransi.
     */
    public function test_limitasi_percobaan_otp_otomatis_menginfiltrasi_sesi_setelah_melebihi_batas(): void
    {
        $unique = 'user_limit_' . substr(uniqid(), -6);
        $user = User::create([
            'nama' => 'User Limit Test',
            'username' => $unique,
            'password_hash' => Hash::make('secret123'),
            'role' => 'KASIR',
            'status' => 'AKTIF',
        ]);

        /** @var OtpService $otpService */
        $otpService = app(OtpService::class);
        $otpService->generate($user->id);

        $sessionData = ['pending_2fa_user_id' => $user->id];

        // 4 percobaan salah pertama
        for ($i = 1; $i <= 4; $i++) {
            $res = $this->withSession($sessionData)->post('/verifikasi', ['code' => '000000']);
            $res->assertSessionHasErrors(['code']);
        }

        // Percobaan ke-5 yang salah -> mencapai batas maksimal 5 kali
        $res5 = $this->withSession($sessionData)->post('/verifikasi', ['code' => '000000']);
        $res5->assertRedirect(route('login'));
        $res5->assertSessionHasErrors(['login']);
        $this->assertNull(session('pending_2fa_user_id'));
    }

    /**
     * 5b. Validasi cooldown 90 detik mencegah spam pengiriman OTP pada /masuk.
     */
    public function test_cooldown_90_detik_mencegah_spam_otp_pada_masuk(): void
    {
        $unique = 'user_spam_' . substr(uniqid(), -6);
        $user = User::create([
            'nama' => 'User Spam Test',
            'username' => $unique,
            'password_hash' => Hash::make('secret123'),
            'role' => 'KASIR',
            'status' => 'AKTIF',
        ]);

        $mockTelegram = Mockery::mock(TelegramService::class);
        $mockTelegram->shouldReceive('send')->once()->andReturn(true);
        $this->app->instance(TelegramService::class, $mockTelegram);

        // Request pertama -> sukses generate OTP
        $res1 = $this->post('/masuk', [
            'username' => $unique,
            'password' => 'secret123',
        ]);
        $res1->assertRedirect(route('verifikasi'));

        // Request kedua dalam interval 90 detik -> ditolak oleh cooldown
        $res2 = $this->post('/masuk', [
            'username' => $unique,
            'password' => 'secret123',
        ]);
        $res2->assertSessionHasErrors(['login']);
    }

    /**
     * 6. Halaman verifikasi merender username yang sebenarnya tanpa sintaks kurung kurawal mentah.
     */
    public function test_halaman_verifikasi_merender_username_sebenarnya_tanpa_kurung_kurawal_mentah(): void
    {
        $unique = 'rossi_' . substr(uniqid(), -6);
        $user = User::create([
            'nama' => 'Muhammad Rossi',
            'username' => $unique,
            'password_hash' => Hash::make('secret123'),
            'role' => 'ADMIN',
            'status' => 'AKTIF',
        ]);

        $response = $this->withSession(['pending_2fa_user_id' => $user->id])
            ->get('/verifikasi');

        $response->assertStatus(200);
        $response->assertSee($unique);
        $response->assertDontSee('@{{ $username }}', false);
        $response->assertDontSee('{{ $username }}', false);
    }
}
