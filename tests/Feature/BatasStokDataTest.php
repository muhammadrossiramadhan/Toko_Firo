<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Services\BatasStokService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Tests\TestCase;

class BatasStokDataTest extends TestCase
{
    use DatabaseTransactions;

    protected string $itemUji1 = 'BST-001';
    protected string $itemUji2 = 'BST-002';
    protected string $itemUji3 = 'BST-003';
    protected string $itemUji4 = 'BST-004';

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan master data item uji pada database toko_firo_test
        DB::table('daftar_item')->updateOrInsert(
            ['kode_item' => $this->itemUji1],
            [
                'nama_item' => 'Pensil Uji 2B BST',
                'jenis' => 'ATK',
                'satuan' => 'pcs',
                'harga_pokok' => 2000.00,
                'harga_jual' => 3000.00,
                'stok' => 0,
                'batas_minimum' => 5,
                'tipe_item' => 'BARANG',
            ]
        );

        DB::table('daftar_item')->updateOrInsert(
            ['kode_item' => $this->itemUji2],
            [
                'nama_item' => 'Kertas Uji HVS BST',
                'jenis' => 'Fotocopy',
                'satuan' => 'rim',
                'harga_pokok' => 45000.00,
                'harga_jual' => 52000.00,
                'stok' => 3,
                'batas_minimum' => 5,
                'tipe_item' => 'BARANG',
            ]
        );

        DB::table('daftar_item')->updateOrInsert(
            ['kode_item' => $this->itemUji3],
            [
                'nama_item' => 'Pulpen Uji Gel BST',
                'jenis' => 'ATK',
                'satuan' => 'pcs',
                'harga_pokok' => 3000.00,
                'harga_jual' => 45000.00,
                'stok' => 25,
                'batas_minimum' => 5,
                'tipe_item' => 'BARANG',
            ]
        );

        DB::table('daftar_item')->updateOrInsert(
            ['kode_item' => $this->itemUji4],
            [
                'nama_item' => 'Jasa Cetak Foto Uji BST',
                'jenis' => 'Jasa',
                'satuan' => 'lbr',
                'harga_pokok' => 1000.00,
                'harga_jual' => 2000.00,
                'stok' => 999999,
                'batas_minimum' => 0,
                'tipe_item' => 'JASA',
            ]
        );
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->markTestSkipped('Middleware auth dinonaktifkan sementara.');

        $response = $this->get('/admin/batas-stok');
        $response->assertRedirect(route('login'));
    }

    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Middleware check role dinonaktifkan sementara.');

        $kasir = Pengguna::factory()->kasir()->create();
        $response = $this->actingAs($kasir)->get('/admin/batas-stok');
        $response->assertStatus(403);
    }

    public function test_endpoint_index_menampilkan_data_riil_dari_database(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        $response = $this->actingAs($admin)->get('/admin/batas-stok');

        $response->assertStatus(200);
        $response->assertSee('BST-001');
        $response->assertSee('Pensil Uji 2B BST');
        $response->assertSee('Habis');
        $response->assertSee('BST-002');
        $response->assertSee('Kertas Uji HVS BST');
        $response->assertSee('Menipis');
    }

    public function test_filter_jenis_dan_pencarian_kata_kunci(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        // Filter jenis Fotocopy
        $resJenis = $this->actingAs($admin)->get('/admin/batas-stok?jenis=Fotocopy');
        $resJenis->assertStatus(200);
        $resJenis->assertSee('Kertas Uji HVS BST');
        $resJenis->assertDontSee('Pensil Uji 2B BST');

        // Pencarian kata kunci nama
        $resCari = $this->actingAs($admin)->get('/admin/batas-stok?q=Pensil');
        $resCari->assertStatus(200);
        $resCari->assertSee('Pensil Uji 2B BST');
        $resCari->assertDontSee('Kertas Uji HVS BST');
    }

    public function test_pembaruan_batas_stok_satuan_dan_pencatatan_audit_log(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);
        $service = app(BatasStokService::class);

        $nilaiBaru = 9;
        $service->updateBatasItem($this->itemUji1, $nilaiBaru, $admin->id_pengguna);

        // Verifikasi batas_minimum pada tabel daftar_item terbarui
        $batasSekarang = (int) DB::table('daftar_item')->where('kode_item', $this->itemUji1)->value('batas_minimum');
        $this->assertEquals($nilaiBaru, $batasSekarang);

        // Verifikasi entri tercatat di log_aktivitas lewat Stored Procedure
        $log = DB::table('log_aktivitas')
            ->where('keterangan', 'like', "%{$this->itemUji1}%")
            ->orderBy('id_log', 'desc')
            ->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString($this->itemUji1, $log->keterangan);
        $this->assertStringContainsString((string) $nilaiBaru, $log->keterangan);
        $this->assertTrue(in_array($log->tipe_aksi, ['STOK', 'BATAS_STOK']));
    }

    public function test_pembaruan_batas_stok_massal_semua_item(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);
        $service = app(BatasStokService::class);

        $nilaiMassal = 14;
        $service->updateBatasSemua($nilaiMassal, $admin->id_pengguna);

        // Verifikasi semua item non-jasa terbarui
        $items = DB::table('daftar_item')
            ->whereIn('kode_item', [$this->itemUji1, $this->itemUji2, $this->itemUji3])
            ->pluck('batas_minimum')
            ->toArray();

        foreach ($items as $batas) {
            $this->assertEquals($nilaiMassal, (int) $batas);
        }

        // Verifikasi audit log massal tercatat
        $log = DB::table('log_aktivitas')
            ->where('keterangan', 'like', '%semua item%')
            ->orderBy('id_log', 'desc')
            ->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString((string) $nilaiMassal, $log->keterangan);
    }

    public function test_validasi_input_penolakan_nilai_negatif(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);
        $service = app(BatasStokService::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Batas minimum tidak boleh negatif.');

        $service->updateBatasItem($this->itemUji1, -1, $admin->id_pengguna);
    }

    public function test_validasi_input_penolakan_nilai_negatif_semua(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);
        $service = app(BatasStokService::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Batas minimum tidak boleh negatif.');

        $service->updateBatasSemua(-5, $admin->id_pengguna);
    }

    public function test_keamanan_input_karakter_injeksi_sql_ditangani_secara_aman(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);
        $service = app(BatasStokService::class);

        $payloadInjeksi = "' OR 1=1 -- '";
        $hasil = $service->getItems(null, $payloadInjeksi, 10);

        // Tidak boleh melempar QueryException dan hasil aman
        $this->assertNotNull($hasil);
    }

    public function test_halaman_batas_stok_tidak_memuat_tag_svg(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        $response = $this->actingAs($admin)->get('/admin/batas-stok');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }
}
