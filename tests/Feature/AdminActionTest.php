<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Services\LaporanPenjualanService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminActionTest extends TestCase
{
    use DatabaseTransactions;

    protected Pengguna $admin;
    protected string $adminUsername = 'adm_action_feat';

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('pengguna')->updateOrInsert(
            ['username' => $this->adminUsername],
            [
                'nama' => 'Admin Action Testing',
                'password_hash' => Hash::make('password123'),
                'role' => 'ADMIN',
                'status' => 'AKTIF',
                'login_terakhir' => now(),
            ]
        );

        $this->admin = Pengguna::where('username', $this->adminUsername)->first();
    }

    /**
     * 1. Seluruh rute panel admin utama wajib fungsional, berstatus 200 OK, dan bebas dari tag <svg>.
     */
    public function test_admin_panel_routes_are_functional_and_accessible(): void
    {
        $routes = [
            route('admin.dashboard'),
            route('admin.barang'),
            route('admin.barang.riwayat'),
            route('admin.pembelian'),
            route('admin.batas-stok'),
            route('admin.laporan.penjualan'),
            route('admin.log'),
            route('admin.notifikasi.index'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->admin)->get($url);
            $response->assertStatus(200);
            $response->assertDontSee('<svg', false);
        }
    }

    /**
     * 2. Form pembelian restock menambah stok barang secara akurat di daftar_item.
     */
    public function test_restock_pembelian_form_increases_item_stock_accurately(): void
    {
        $kodeItem = 'ACT-BRG-RESTOCK';
        DB::table('daftar_item')->updateOrInsert(
            ['kode_item' => $kodeItem],
            [
                'nama_item' => 'Barang Restock Test',
                'jenis' => 'ATK',
                'satuan' => 'pcs',
                'harga_pokok' => 5000,
                'harga_jual' => 7000,
                'stok' => 10,
                'batas_minimum' => 5,
            ]
        );

        $response = $this->actingAs($this->admin)->post(route('admin.pembelian.store'), [
            'nama_supplier' => 'Supplier Uji Utama',
            'kode_item' => $kodeItem,
            'jumlah' => 15,
        ]);

        $response->assertRedirect(route('admin.pembelian'));

        $item = DB::table('daftar_item')->where('kode_item', $kodeItem)->first();
        $this->assertEquals(25, (int) $item->stok);

        $pembelian = DB::table('daftar_pembelian')->where('nama_supplier', 'Supplier Uji Utama')->first();
        $this->assertNotNull($pembelian);
        $this->assertEquals(15 * 5000, (float) $pembelian->total_bayar);
    }

    /**
     * 3. Mutasi batas stok ditolak pada method GET (SEC-06) dan diterima via POST/PATCH.
     */
    public function test_batas_stok_mutation_rejected_via_get_and_accepted_via_post_patch(): void
    {
        $kodeItem = 'ACT-BRG-BATAS';
        DB::table('daftar_item')->updateOrInsert(
            ['kode_item' => $kodeItem],
            [
                'nama_item' => 'Barang Batas Stok Test',
                'jenis' => 'Kertas',
                'satuan' => 'rim',
                'harga_pokok' => 30000,
                'harga_jual' => 35000,
                'stok' => 20,
                'batas_minimum' => 5,
            ]
        );

        // SEC-06: GET must not mutate state even if parameters are supplied
        $this->actingAs($this->admin)->get(route('admin.batas-stok', [
            'stok_minimum' => 99,
            'kode_item' => $kodeItem,
            'aksi' => 'update',
        ]));

        $itemAwal = DB::table('daftar_item')->where('kode_item', $kodeItem)->first();
        $this->assertEquals(5, (int) $itemAwal->batas_minimum);

        // POST / PATCH to /admin/batas-stok/simpan mutates state legitimately
        $responsePost = $this->actingAs($this->admin)->postJson(route('admin.batas-stok.simpan'), [
            'kode_item' => $kodeItem,
            'batas_minimum' => 18,
        ]);

        $responsePost->assertStatus(200);

        $itemAkhir = DB::table('daftar_item')->where('kode_item', $kodeItem)->first();
        $this->assertEquals(18, (int) $itemAkhir->batas_minimum);
    }

    /**
     * 4. Riwayat transaksi barang membaca data riil dari database (bukan mockup statis).
     */
    public function test_barang_transaction_history_reads_real_database_transactions(): void
    {
        $kodeTrx = 'PJ-ACT-' . rand(1000, 9999);
        $kodeItem = 'ACT-BRG-HIST';

        DB::table('daftar_item')->updateOrInsert(
            ['kode_item' => $kodeItem],
            [
                'nama_item' => 'Buku Kas Riwayat Uji',
                'jenis' => 'Buku',
                'satuan' => 'buku',
                'harga_pokok' => 10000,
                'harga_jual' => 15000,
                'stok' => 50,
                'batas_minimum' => 5,
            ]
        );

        DB::table('daftar_penjualan')->insert([
            'kode_transaksi' => $kodeTrx,
            'nama_pelanggan' => 'Umum',
            'tanggal_transaksi' => now()->toDateString(),
            'id_kasir' => $this->admin->id_pengguna,
            'total_bayar' => 30000,
            'metode_bayar' => 'QRIS',
        ]);

        DB::table('daftar_transaksi')->insert([
            'kode_penjualan' => $kodeTrx,
            'kode_item' => $kodeItem,
            'item_keluar' => 2,
            'jumlah_item' => 2,
            'subtotal' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.barang.riwayat'));
        $response->assertStatus(200);
        $response->assertSee($kodeTrx);
        $response->assertSee('Buku Kas Riwayat Uji');
        $response->assertSee('QRIS');
    }

    /**
     * 5. Ekspor CSV log aktivitas menetralkan formula injection (CWE-1236).
     */
    public function test_activity_log_csv_export_neutralizes_formula_injection_cwe_1236(): void
    {
        $payloadInjection = '=CMD|"/C calc"!A0';
        $keteranganInjection = '@SUM(A1:A5)+123';

        DB::table('log_aktivitas')->insert([
            'id_pengguna' => $this->admin->id_pengguna,
            'tipe_aksi' => $payloadInjection,
            'keterangan' => $keteranganInjection,
            'waktu' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.log', ['export' => 'csv']));
        $response->assertStatus(200);

        $csvContent = $response->streamedContent();

        // CWE-1236: cell starting with =, +, -, @, \t, \r must be neutralized with a leading apostrophe '
        $this->assertStringContainsString("'=CMD", $csvContent);
        $this->assertStringContainsString("'@SUM", $csvContent);
    }

    /**
     * 6. Laporan penjualan mencerminkan metode pembayaran riil database (TUNAI / QRIS).
     */
    public function test_sales_report_reflects_real_payment_methods(): void
    {
        $kodeTrxQris = 'PJ-QRIS-' . rand(1000, 9999);
        $kodeItem = 'ACT-BRG-METODE';

        DB::table('daftar_item')->updateOrInsert(
            ['kode_item' => $kodeItem],
            [
                'nama_item' => 'Item Metode Test',
                'jenis' => 'ATK',
                'satuan' => 'pcs',
                'harga_pokok' => 5000,
                'harga_jual' => 8000,
                'stok' => 10,
                'batas_minimum' => 2,
            ]
        );

        $tgl = now()->toDateString();

        DB::table('daftar_penjualan')->insert([
            'kode_transaksi' => $kodeTrxQris,
            'nama_pelanggan' => 'Pelanggan QRIS',
            'tanggal_transaksi' => $tgl,
            'id_kasir' => $this->admin->id_pengguna,
            'total_bayar' => 16000,
            'metode_bayar' => 'QRIS',
        ]);

        DB::table('daftar_transaksi')->insert([
            'kode_penjualan' => $kodeTrxQris,
            'kode_item' => $kodeItem,
            'item_keluar' => 2,
            'jumlah_item' => 2,
            'subtotal' => 16000,
        ]);

        $laporanService = app(LaporanPenjualanService::class);
        $transaksi = $laporanService->transaksi($tgl, $tgl, 10);

        $matched = collect($transaksi->items())->firstWhere('kode_transaksi', $kodeTrxQris);
        $this->assertNotNull($matched);
        $this->assertEquals('QRIS', strtoupper($matched->metode));
    }

    /**
     * 7. Toggle notifikasi menolak key di luar whitelist (SEC-19) dengan HTTP 422.
     */
    public function test_notification_toggle_rejects_unwhitelisted_keys(): void
    {
        // 1. Unwhitelisted key must be rejected with 422
        $responseBad = $this->actingAs($this->admin)->postJson(route('admin.notifikasi.toggle'), [
            'key' => 'unauthorized_dangerous_key',
            'aktif' => true,
        ]);

        $responseBad->assertStatus(422);

        // 2. Whitelisted key must be accepted with 200
        $responseGood = $this->actingAs($this->admin)->postJson(route('admin.notifikasi.toggle'), [
            'key' => 'laporan_otomatis',
            'aktif' => true,
        ]);

        $responseGood->assertStatus(200);
        $this->assertTrue($responseGood->json('aktif'));
    }
}
