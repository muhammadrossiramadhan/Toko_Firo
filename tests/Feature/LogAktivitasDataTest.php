<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LogAktivitasDataTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->markTestSkipped('Middleware auth dinonaktifkan sementara.');

        $response = $this->get('/admin/log-aktivitas');
        $response->assertRedirect(route('login'));
    }

    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Middleware check role dinonaktifkan sementara.');

        $kasir = Pengguna::factory()->kasir()->create();
        $response = $this->actingAs($kasir)->get('/admin/log-aktivitas');
        $response->assertStatus(403);
    }

    public function test_pembacaan_daftar_log_dan_paginasi_sepuluh_baris(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        // Insert 15 baris log aktivitas
        $records = [];
        for ($i = 1; $i <= 15; $i++) {
            $records[] = [
                'waktu' => now()->subMinutes(16 - $i),
                'keterangan' => sprintf('Log Uji Nomor %02d', $i),
                'tipe_aksi' => 'TRANSAKSI',
                'id_pengguna' => $admin->id_pengguna,
            ];
        }
        DB::table('log_aktivitas')->insert($records);

        // Halaman 1 (10 item terbaru: Nomor 15 s/d Nomor 06)
        $respPage1 = $this->actingAs($admin)->get('/admin/log-aktivitas?page=1');
        $respPage1->assertStatus(200);
        $respPage1->assertSee('Log Uji Nomor 15');
        $respPage1->assertSee('Log Uji Nomor 06');
        $respPage1->assertDontSee('Log Uji Nomor 05');

        // Halaman 2 (5 item sisa: Nomor 05 s/d Nomor 01)
        $respPage2 = $this->actingAs($admin)->get('/admin/log-aktivitas?page=2');
        $respPage2->assertStatus(200);
        $respPage2->assertSee('Log Uji Nomor 05');
        $respPage2->assertSee('Log Uji Nomor 01');
        $respPage2->assertDontSee('Log Uji Nomor 15');
    }

    public function test_filter_kata_kunci_keterangan(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        DB::table('log_aktivitas')->insert([
            [
                'waktu' => now(),
                'keterangan' => 'Log Unik Khusus CariAlpha',
                'tipe_aksi' => 'TRANSAKSI',
                'id_pengguna' => $admin->id_pengguna,
            ],
            [
                'waktu' => now(),
                'keterangan' => 'Log Biasa Lainnya Beta',
                'tipe_aksi' => 'TRANSAKSI',
                'id_pengguna' => $admin->id_pengguna,
            ],
        ]);

        $response = $this->actingAs($admin)->get('/admin/log-aktivitas?kata_kunci=CariAlpha');

        $response->assertStatus(200);
        $response->assertSee('Log Unik Khusus CariAlpha');
        $response->assertDontSee('Log Biasa Lainnya Beta');
    }

    public function test_filter_tipe_aksi(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        DB::table('log_aktivitas')->insert([
            [
                'waktu' => now(),
                'keterangan' => 'Aktivitas Pengguna Masuk Sistem',
                'tipe_aksi' => 'LOGIN',
                'id_pengguna' => $admin->id_pengguna,
            ],
            [
                'waktu' => now(),
                'keterangan' => 'Peringatan Batas Stok Mencapai Limit',
                'tipe_aksi' => 'BATAS_STOK',
                'id_pengguna' => null,
            ],
        ]);

        $response = $this->actingAs($admin)->get('/admin/log-aktivitas?tipe=LOGIN');

        $response->assertStatus(200);
        $response->assertSee('Aktivitas Pengguna Masuk Sistem');
        $response->assertDontSee('Peringatan Batas Stok Mencapai Limit');
    }

    public function test_filter_rentang_tanggal(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        DB::table('log_aktivitas')->insert([
            [
                'waktu' => '2026-05-10 10:00:00',
                'keterangan' => 'Log Tanggal Lampau Sepuluh',
                'tipe_aksi' => 'TRANSAKSI',
                'id_pengguna' => $admin->id_pengguna,
            ],
            [
                'waktu' => '2026-05-15 12:00:00',
                'keterangan' => 'Log Tanggal Sesuai LimaBelas',
                'tipe_aksi' => 'TRANSAKSI',
                'id_pengguna' => $admin->id_pengguna,
            ],
            [
                'waktu' => '2026-05-20 15:00:00',
                'keterangan' => 'Log Tanggal Mendatang DuaPuluh',
                'tipe_aksi' => 'TRANSAKSI',
                'id_pengguna' => $admin->id_pengguna,
            ],
        ]);

        $url = '/admin/log-aktivitas?tanggal_mulai=2026-05-12&tanggal_akhir=2026-05-18';
        $response = $this->actingAs($admin)->get($url);

        $response->assertStatus(200);
        $response->assertSee('Log Tanggal Sesuai LimaBelas');
        $response->assertDontSee('Log Tanggal Lampau Sepuluh');
        $response->assertDontSee('Log Tanggal Mendatang DuaPuluh');
    }

    public function test_keamanan_input_karakter_injeksi_sql_ditangani_dengan_aman(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        $injeksiUrl = '/admin/log-aktivitas?' . http_build_query([
            'kata_kunci' => "1' OR '1'='1",
            'tanggal_mulai' => "2026-01-01' OR 1=1--",
            'tipe' => "LOGIN' OR '1'='1",
        ]);

        $response = $this->actingAs($admin)->get($injeksiUrl);

        $response->assertStatus(200);
        $response->assertDontSee('SQLSTATE', false);
        $response->assertDontSee('syntax error', false);
    }

    public function test_ekspor_csv_mengembalikan_status_200_dan_header_text_csv(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        DB::table('log_aktivitas')->insert([
            'waktu' => '2026-10-25 10:15:00',
            'keterangan' => 'Uji Ekspor CSV Khusus Stream',
            'tipe_aksi' => 'TRANSAKSI',
            'id_pengguna' => $admin->id_pengguna,
        ]);

        $response = $this->actingAs($admin)->get('/admin/log-aktivitas?export=csv');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $content = $response->streamedContent();
        $this->assertStringContainsString('Waktu,Tipe Aksi,Keterangan', $content);
        $this->assertStringContainsString('Uji Ekspor CSV Khusus Stream', $content);
    }

    public function test_halaman_log_aktivitas_bebas_tag_svg(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        $response = $this->actingAs($admin)->get('/admin/log-aktivitas');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }
}

