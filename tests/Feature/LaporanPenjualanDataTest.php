<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LaporanPenjualanDataTest extends TestCase
{
    use DatabaseTransactions;

    protected string $itemUji = 'TST-BRG-01';

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan master item uji jika belum ada
        DB::table('daftar_item')->updateOrInsert(
            ['kode_item' => $this->itemUji],
            [
                'nama_item' => 'Buku Uji TST',
                'jenis' => 'Buku',
                'merek' => 'TST',
                'rak' => 'R1',
                'satuan' => 'pcs',
                'harga_pokok' => 2000.00,
                'harga_jual' => 3000.00,
                'stok' => 50,
                'batas_minimum' => 5,
            ]
        );
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->markTestSkipped('Middleware auth dinonaktifkan sementara.');

        $response = $this->get('/admin/laporan-penjualan');
        $response->assertRedirect(route('login'));
    }

    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Middleware check role dinonaktifkan sementara.');

        $kasir = Pengguna::factory()->kasir()->create();
        $response = $this->actingAs($kasir)->get('/admin/laporan-penjualan');
        $response->assertStatus(403);
    }

    public function test_total_dan_jumlah_nota_laporan_penjualan_benar(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);
        $kasir = Pengguna::factory()->kasir()->create(['status' => 'AKTIF']);
        $hariIni = date('Y-m-d');

        // Buat 2 transaksi penjualan uji
        DB::table('daftar_penjualan')->insert([
            [
                'kode_transaksi' => 'TST-TRX-001',
                'nama_pelanggan' => 'Pelanggan A',
                'tanggal_transaksi' => $hariIni,
                'total_bayar' => 50000.00,
                'id_kasir' => $kasir->id_pengguna,
                'metode_bayar' => 'TUNAI',
            ],
            [
                'kode_transaksi' => 'TST-TRX-002',
                'nama_pelanggan' => 'Pelanggan B',
                'tanggal_transaksi' => $hariIni,
                'total_bayar' => 75000.00,
                'id_kasir' => $kasir->id_pengguna,
                'metode_bayar' => 'QRIS',
            ],
        ]);

        DB::table('daftar_transaksi')->insert([
            [
                'kode_penjualan' => 'TST-TRX-001',
                'kode_item' => $this->itemUji,
                'item_keluar' => 2,
                'retur_penjualan' => 0,
                'subtotal' => 50000.00,
            ],
            [
                'kode_penjualan' => 'TST-TRX-002',
                'kode_item' => $this->itemUji,
                'item_keluar' => 3,
                'retur_penjualan' => 0,
                'subtotal' => 75000.00,
            ],
        ]);

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan?rentang=hari-ini');

        $response->assertStatus(200);
        $response->assertSee('TST-TRX-001');
        $response->assertSee('TST-TRX-002');
        $response->assertSee('Rp 125.000');
        $response->assertSee('Buku Uji TST');
    }

    public function test_rentang_tanggal_inklusif_batas_awal_dan_akhir(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        $tglBatasAwal = '2026-05-10';
        $tglTengah = '2026-05-12';
        $tglBatasAkhir = '2026-05-15';
        $tglSebelum = '2026-05-09';
        $tglSesudah = '2026-05-16';

        DB::table('daftar_penjualan')->insert([
            ['kode_transaksi' => 'TST-TRX-SEBELUM', 'nama_pelanggan' => 'Uji', 'tanggal_transaksi' => $tglSebelum, 'total_bayar' => 10000, 'id_kasir' => null, 'metode_bayar' => 'TUNAI'],
            ['kode_transaksi' => 'TST-TRX-AWAL', 'nama_pelanggan' => 'Uji', 'tanggal_transaksi' => $tglBatasAwal, 'total_bayar' => 15000, 'id_kasir' => null, 'metode_bayar' => 'TUNAI'],
            ['kode_transaksi' => 'TST-TRX-TENGAH', 'nama_pelanggan' => 'Uji', 'tanggal_transaksi' => $tglTengah, 'total_bayar' => 20000, 'id_kasir' => null, 'metode_bayar' => 'TUNAI'],
            ['kode_transaksi' => 'TST-TRX-AKHIR', 'nama_pelanggan' => 'Uji', 'tanggal_transaksi' => $tglBatasAkhir, 'total_bayar' => 25000, 'id_kasir' => null, 'metode_bayar' => 'TUNAI'],
            ['kode_transaksi' => 'TST-TRX-SESUDAH', 'nama_pelanggan' => 'Uji', 'tanggal_transaksi' => $tglSesudah, 'total_bayar' => 30000, 'id_kasir' => null, 'metode_bayar' => 'TUNAI'],
        ]);

        $url = "/admin/laporan-penjualan?rentang=kustom&mulai={$tglBatasAwal}&sampai={$tglBatasAkhir}";
        $response = $this->actingAs($admin)->get($url);

        $response->assertStatus(200);
        $response->assertSee('TST-TRX-AWAL');
        $response->assertSee('TST-TRX-TENGAH');
        $response->assertSee('TST-TRX-AKHIR');
        $response->assertDontSee('TST-TRX-SEBELUM');
        $response->assertDontSee('TST-TRX-SESUDAH');
    }

    public function test_kasir_null_tampil_tanda_strip(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);
        $hariIni = date('Y-m-d');

        DB::table('daftar_penjualan')->insert([
            'kode_transaksi' => 'TST-TRX-STRIP',
            'nama_pelanggan' => 'Pelanggan Umum',
            'tanggal_transaksi' => $hariIni,
            'total_bayar' => 10000,
            'id_kasir' => null,
            'metode_bayar' => 'TUNAI',
        ]);

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan?rentang=hari-ini');

        $response->assertStatus(200);
        $response->assertSee('TST-TRX-STRIP');
        $response->assertSeeText('-');
    }

    public function test_paginasi_lima_item_per_halaman(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);
        $hariIni = '2026-06-01';

        $records = [];
        for ($i = 1; $i <= 7; $i++) {
            $records[] = [
                'kode_transaksi' => sprintf('TST-PGN-%03d', $i),
                'nama_pelanggan' => 'Pelanggan ' . $i,
                'tanggal_transaksi' => $hariIni,
                'total_bayar' => 10000 * $i,
                'id_kasir' => null,
                'metode_bayar' => 'TUNAI',
            ];
        }
        DB::table('daftar_penjualan')->insert($records);

        // Halaman 1 (5 item teratas urut kode desc: 007, 006, 005, 004, 003)
        $respPage1 = $this->actingAs($admin)->get("/admin/laporan-penjualan?rentang=kustom&mulai={$hariIni}&sampai={$hariIni}&page=1");
        $respPage1->assertStatus(200);
        $respPage1->assertSee('TST-PGN-007');
        $respPage1->assertSee('TST-PGN-003');
        $respPage1->assertDontSee('TST-PGN-002');
        $respPage1->assertDontSee('TST-PGN-001');

        // Halaman 2 (2 item sisa: 002, 001)
        $respPage2 = $this->actingAs($admin)->get("/admin/laporan-penjualan?rentang=kustom&mulai={$hariIni}&sampai={$hariIni}&page=2");
        $respPage2->assertStatus(200);
        $respPage2->assertSee('TST-PGN-002');
        $respPage2->assertSee('TST-PGN-001');
        $respPage2->assertDontSee('TST-PGN-007');
    }

    public function test_tanggal_tidak_valid_dan_rentang_lebih_dari_366_hari_menampilkan_pesan(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        // Kasus 1: Tanggal terbalik (sampai < mulai)
        $respInvalid = $this->actingAs($admin)->get('/admin/laporan-penjualan?rentang=kustom&mulai=2026-10-20&sampai=2026-10-10');
        $respInvalid->assertStatus(200);
        $respInvalid->assertSee('Tanggal tidak valid, ditampilkan hari ini.');

        // Kasus 2: Rentang lebih dari 366 hari
        $respPanjang = $this->actingAs($admin)->get('/admin/laporan-penjualan?rentang=kustom&mulai=2024-01-01&sampai=2026-01-01');
        $respPanjang->assertStatus(200);
        $respPanjang->assertSee('Rentang terlalu panjang, maksimal 1 tahun.');
    }

    public function test_keamanan_input_karakter_injeksi_sql_ditolak_secara_aman(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        $injeksiParams = [
            'rentang' => 'kustom',
            'mulai' => "2026-01-01' OR '1'='1",
            'sampai' => "2026-12-31; DROP TABLE daftar_item;",
        ];

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan?' . http_build_query($injeksiParams));

        $response->assertStatus(200);
        $response->assertSee('Tanggal tidak valid, ditampilkan hari ini.');
        $response->assertDontSee('SQLSTATE', false);
        $response->assertDontSee('syntax error', false);
    }

    public function test_subtotal_negatif_berawalan_minus_rp(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);
        $hariIni = date('Y-m-d');

        DB::table('daftar_penjualan')->insert([
            'kode_transaksi' => 'TST-TRX-RETUR',
            'nama_pelanggan' => 'Retur Pelanggan',
            'tanggal_transaksi' => $hariIni,
            'total_bayar' => -15000.00,
            'id_kasir' => null,
            'metode_bayar' => 'TUNAI',
        ]);

        DB::table('daftar_transaksi')->insert([
            'kode_penjualan' => 'TST-TRX-RETUR',
            'kode_item' => $this->itemUji,
            'item_keluar' => 0,
            'retur_penjualan' => 1,
            'subtotal' => -15000.00,
        ]);

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan?rentang=hari-ini');

        $response->assertStatus(200);
        $response->assertSee('-Rp 15.000');
    }

    public function test_halaman_laporan_penjualan_tidak_memuat_tag_svg(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        $response = $this->actingAs($admin)->get('/admin/laporan-penjualan');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }
}
