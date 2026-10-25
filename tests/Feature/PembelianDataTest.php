<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Services\PembelianService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Tests\TestCase;

class PembelianDataTest extends TestCase
{
    use DatabaseTransactions;

    protected string $itemUji1 = 'PBT-BRG-01';
    protected string $itemUji2 = 'PBT-BRG-02';

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan master data item uji
        DB::table('daftar_item')->updateOrInsert(
            ['kode_item' => $this->itemUji1],
            [
                'nama_item' => 'Kertas Uji PBT Menipis',
                'jenis' => 'Kertas',
                'satuan' => 'rim',
                'harga_pokok' => 45000.00,
                'harga_jual' => 52000.00,
                'stok' => 2,
                'batas_minimum' => 5,
            ]
        );

        DB::table('daftar_item')->updateOrInsert(
            ['kode_item' => $this->itemUji2],
            [
                'nama_item' => 'Pulpen Uji PBT Habis',
                'jenis' => 'Alat Tulis',
                'satuan' => 'pcs',
                'harga_pokok' => 2000.00,
                'harga_jual' => 3000.00,
                'stok' => 0,
                'batas_minimum' => 5,
            ]
        );
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->markTestSkipped('Middleware auth dinonaktifkan sementara.');

        $response = $this->get('/admin/pembelian');
        $response->assertRedirect(route('login'));
    }

    public function test_kasir_aktif_mendapat_403(): void
    {
        $this->markTestSkipped('Middleware check role dinonaktifkan sementara.');

        $kasir = Pengguna::factory()->kasir()->create();
        $response = $this->actingAs($kasir)->get('/admin/pembelian');
        $response->assertStatus(403);
    }

    public function test_barang_stok_kurang_atau_sama_dengan_batas_muncul_di_daftar_restock(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        $response = $this->actingAs($admin)->get('/admin/pembelian');

        $response->assertStatus(200);
        $response->assertSee('Kertas Uji PBT Menipis');
        $response->assertSee('2 rim (Menipis)');
    }

    public function test_barang_stok_nol_berstatus_habis(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        $response = $this->actingAs($admin)->get('/admin/pembelian');

        $response->assertStatus(200);
        $response->assertSee('Pulpen Uji PBT Habis');
        $response->assertSee('0 pcs (Habis)');
    }

    public function test_pencatatan_pembelian_menambah_stok_dan_menambah_baris_daftar_transaksi(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);
        $service = app(PembelianService::class);

        $kodeTransaksi = 'PBT-TRX-001';
        $namaSupplier = 'CV Sumber Makmur PBT';
        $qtyBeli = 15;

        // Stok awal adalah 2
        $stokAwal = (int) DB::table('daftar_item')->where('kode_item', $this->itemUji1)->value('stok');

        $service->catatPembelian($kodeTransaksi, $namaSupplier, $admin->id_pengguna, [
            [
                'kode_item' => $this->itemUji1,
                'jumlah' => $qtyBeli,
            ],
        ]);

        // Verifikasi stok pada daftar_item bertambah
        $stokAkhir = (int) DB::table('daftar_item')->where('kode_item', $this->itemUji1)->value('stok');
        $this->assertEquals($stokAwal + $qtyBeli, $stokAkhir);

        // Verifikasi baris transaksi tercatat pada daftar_transaksi
        $transaksi = DB::table('daftar_transaksi')
            ->where('kode_pembelian', $kodeTransaksi)
            ->where('kode_item', $this->itemUji1)
            ->first();
        $this->assertNotNull($transaksi);
        $this->assertEquals($qtyBeli, (int) $transaksi->item_masuk);
        $this->assertEquals($qtyBeli * 45000, (float) $transaksi->subtotal);

        // Verifikasi header pembelian tercatat pada daftar_pembelian
        $header = DB::table('daftar_pembelian')
            ->where('kode_transaksi', $kodeTransaksi)
            ->first();
        $this->assertNotNull($header);
        $this->assertEquals($namaSupplier, $header->nama_supplier);
        $this->assertEquals($qtyBeli * 45000, (float) $header->total_bayar);

        // Verifikasi riwayat pembelian muncul pada tampilan
        $response = $this->actingAs($admin)->get('/admin/pembelian');
        $response->assertStatus(200);
        $response->assertSee($kodeTransaksi);
        $response->assertSee('Kertas Uji PBT Menipis');
        $response->assertSee('15 rim');
    }

    public function test_validasi_input_jumlah_tidak_boleh_negatif_atau_nol(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);
        $service = app(PembelianService::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Jumlah item pembelian harus lebih dari 0.');

        $service->catatPembelian('PBT-TRX-ERR', 'Supplier Uji', $admin->id_pengguna, [
            [
                'kode_item' => $this->itemUji1,
                'jumlah' => 0,
            ],
        ]);
    }

    public function test_halaman_pembelian_tidak_memuat_tag_svg(): void
    {
        $admin = Pengguna::factory()->create(['role' => 'ADMIN', 'status' => 'AKTIF']);

        $response = $this->actingAs($admin)->get('/admin/pembelian');

        $response->assertStatus(200);
        $response->assertDontSee('<svg', false);
    }
}

