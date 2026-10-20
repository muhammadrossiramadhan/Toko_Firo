<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestroyBarangRequest;
use App\Http\Requests\Admin\StoreBarangRequest;
use App\Http\Requests\Admin\UpdateBarangRequest;
use App\Http\Requests\Admin\UpdateHargaBarangRequest;
use App\Models\ItemBarang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar barang dari database dengan paginasi, pencarian, dan filter.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $jenis = (string) $request->query('jenis', 'Semua');
        $urut = (string) $request->query('urut', 'terbaru');

        $query = ItemBarang::query();

        // Pencarian (q): cocok sebagian pada kode_item atau nama_item
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kode_item', 'like', "%{$q}%")
                  ->orWhere('nama_item', 'like', "%{$q}%");
            });
        }

        // Filter Jenis
        if ($jenis !== '' && $jenis !== 'Semua') {
            $query->where('jenis', $jenis);
        }

        // Pengurutan
        if ($urut === 'nama') {
            $query->orderBy('nama_item', 'asc');
        } elseif ($urut === 'stok') {
            $query->orderBy('stok', 'asc');
        } else {
            // Default: terbaru (dibuat_pada menurun)
            $query->orderBy('dibuat_pada', 'desc');
        }

        $items = $query->paginate(7)->withQueryString();

        $daftarJenis = ItemBarang::query()
            ->whereNotNull('jenis')
            ->where('jenis', '!=', '')
            ->distinct()
            ->orderBy('jenis', 'asc')
            ->pluck('jenis');

        $daftarSatuan = ItemBarang::query()
            ->whereNotNull('satuan')
            ->where('satuan', '!=', '')
            ->distinct()
            ->orderBy('satuan', 'asc')
            ->pluck('satuan');

        return view('admin.barang.index', [
            'items' => $items,
            'daftarJenis' => $daftarJenis,
            'daftarSatuan' => $daftarSatuan,
            'q' => $q,
            'jenis' => $jenis,
            'urut' => $urut,
        ]);
    }

    /**
     * Menyimpan barang baru menggunakan stored procedure sp_create_item.
     */
    public function store(StoreBarangRequest $request): RedirectResponse
    {
        $tipe = strtoupper($request->validated('tipe_item')) === 'JASA' ? 'JASA' : 'BARANG';
        $stok = $tipe === 'JASA' ? 0 : (int) ($request->validated('stok_awal') ?? 0);
        $batas = $request->validated('batas_minimum') !== null && $request->validated('batas_minimum') !== ''
            ? (int) $request->validated('batas_minimum')
            : 5;
        $idPengguna = Auth::id() ?? Auth::user()?->id_pengguna ?? 1;
        $kode = $request->validated('kode_item');
        $nama = $request->validated('nama_item');

        try {
            DB::statement('CALL sp_create_item(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $kode,
                $nama,
                $request->validated('jenis'),
                $request->validated('merek'),
                $request->validated('rak'),
                $request->validated('satuan'),
                $request->validated('harga_pokok'),
                $request->validated('harga_jual'),
                $stok,
                $tipe,
                $request->validated('keterangan'),
                $batas,
                $idPengguna,
            ]);
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), '1267') || str_contains($e->getMessage(), 'Illegal mix of collations')) {
                if (ItemBarang::where('kode_item', $kode)->exists()) {
                    return redirect()->route('admin.barang')
                        ->withInput()
                        ->with('error', 'Kode barang sudah dipakai.')
                        ->with('open_modal', 'tambah');
                }

                $stokTersimpan = $tipe === 'JASA' ? 999999 : $stok;
                ItemBarang::create([
                    'kode_item' => $kode,
                    'nama_item' => $nama,
                    'jenis' => $request->validated('jenis'),
                    'merek' => $request->validated('merek'),
                    'rak' => $request->validated('rak'),
                    'satuan' => $request->validated('satuan'),
                    'tipe_item' => $tipe,
                    'harga_pokok' => $request->validated('harga_pokok'),
                    'harga_jual' => $request->validated('harga_jual'),
                    'stok' => $stokTersimpan,
                    'keterangan' => $request->validated('keterangan'),
                    'batas_minimum' => $batas,
                    'dibuat_pada' => now(),
                    'diubah_pada' => now(),
                ]);

                DB::table('log_aktivitas')->insert([
                    'waktu' => now(),
                    'keterangan' => 'Item baru: ' . $kode . ' - ' . $nama,
                    'tipe_aksi' => 'ITEM_BARU',
                    'id_pengguna' => $idPengguna,
                    'data_sebelum' => null,
                    'data_sesudah' => null,
                ]);
            } else {
                return redirect()->route('admin.barang')
                    ->withInput()
                    ->with('error', $this->terjemahkanError($e))
                    ->with('open_modal', 'tambah');
            }
        }

        return redirect()->route('admin.barang')->with('sukses', 'Barang berhasil ditambahkan.');
    }

    /**
     * Memperbarui data umum barang menggunakan stored procedure sp_update_item_v2.
     */
    public function update(UpdateBarangRequest $request): RedirectResponse
    {
        $tipe = strtoupper($request->validated('tipe_item')) === 'JASA' ? 'JASA' : 'BARANG';
        $batas = $request->validated('batas_minimum') !== null && $request->validated('batas_minimum') !== ''
            ? (int) $request->validated('batas_minimum')
            : 5;
        $idPengguna = Auth::id() ?? Auth::user()?->id_pengguna ?? 1;
        $kode = $request->validated('kode_item');
        $nama = $request->validated('nama_item');

        try {
            DB::statement('CALL sp_update_item_v2(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $kode,
                $nama,
                $request->validated('jenis'),
                $request->validated('merek'),
                $request->validated('rak'),
                $request->validated('satuan'),
                $tipe,
                $request->validated('keterangan'),
                $batas,
                $idPengguna,
            ]);
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), '1267') || str_contains($e->getMessage(), 'Illegal mix of collations')) {
                $item = ItemBarang::where('kode_item', $kode)->first();
                if (! $item) {
                    return redirect()->route('admin.barang')->with('error', 'Barang tidak ditemukan.');
                }

                $item->update([
                    'nama_item' => $nama,
                    'jenis' => $request->validated('jenis'),
                    'merek' => $request->validated('merek'),
                    'rak' => $request->validated('rak'),
                    'satuan' => $request->validated('satuan'),
                    'tipe_item' => $tipe,
                    'keterangan' => $request->validated('keterangan'),
                    'batas_minimum' => $batas,
                    'diubah_pada' => now(),
                ]);

                DB::table('log_aktivitas')->insert([
                    'waktu' => now(),
                    'keterangan' => 'Item diubah: ' . $kode . ' - ' . $nama,
                    'tipe_aksi' => 'ITEM_DIUBAH',
                    'id_pengguna' => $idPengguna,
                    'data_sebelum' => null,
                    'data_sesudah' => null,
                ]);
            } else {
                return redirect()->route('admin.barang')
                    ->withInput()
                    ->with('error', $this->terjemahkanError($e));
            }
        }

        return redirect()->route('admin.barang')->with('sukses', 'Data barang berhasil diubah.');
    }

    /**
     * Memperbarui harga barang menggunakan stored procedure sp_update_harga.
     */
    public function updateHarga(UpdateHargaBarangRequest $request): RedirectResponse
    {
        $idPengguna = Auth::id() ?? Auth::user()?->id_pengguna ?? 1;
        $kode = $request->validated('kode_item');
        $hargaPokok = $request->validated('harga_pokok');
        $hargaJual = $request->validated('harga_jual');

        try {
            DB::statement('CALL sp_update_harga(?, ?, ?, ?)', [
                $kode,
                $hargaPokok,
                $hargaJual,
                $idPengguna,
            ]);
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), '1267') || str_contains($e->getMessage(), 'Illegal mix of collations')) {
                $item = ItemBarang::where('kode_item', $kode)->first();
                if (! $item) {
                    return redirect()->route('admin.barang')->with('error', 'Barang tidak ditemukan.');
                }

                $item->update([
                    'harga_pokok' => $hargaPokok,
                    'harga_jual' => $hargaJual,
                    'diubah_pada' => now(),
                ]);

                DB::table('log_aktivitas')->insert([
                    'waktu' => now(),
                    'keterangan' => 'Ubah harga: ' . $kode . ' - ' . $item->nama_item,
                    'tipe_aksi' => 'HARGA_DIUBAH',
                    'id_pengguna' => $idPengguna,
                    'data_sebelum' => null,
                    'data_sesudah' => null,
                ]);
            } else {
                return redirect()->route('admin.barang')
                    ->withInput()
                    ->with('error', $this->terjemahkanError($e));
            }
        }

        return redirect()->route('admin.barang')->with('sukses', 'Harga barang berhasil diubah.');
    }

    /**
     * Menghapus barang menggunakan stored procedure sp_delete_item_v2.
     */
    public function destroy(DestroyBarangRequest $request): RedirectResponse
    {
        $idPengguna = Auth::id() ?? Auth::user()?->id_pengguna ?? 1;
        $kode = $request->validated('kode_item');

        // Pemeriksaan awal riwayat transaksi sesuai K13
        if (DB::table('daftar_transaksi')->where('kode_item', $kode)->exists()) {
            return redirect()->route('admin.barang')
                ->with('error', 'Barang tidak bisa dihapus karena sudah pernah dijual.');
        }

        try {
            DB::statement('CALL sp_delete_item_v2(?, ?)', [
                $kode,
                $idPengguna,
            ]);
        } catch (Throwable $e) {
            $pesan = $e->getMessage();

            if (str_contains($pesan, 'riwayat transaksi') || str_contains($pesan, 'sudah memiliki riwayat transaksi')) {
                return redirect()->route('admin.barang')
                    ->with('error', 'Barang tidak bisa dihapus karena sudah pernah dijual.');
            }

            if (str_contains($pesan, '1267') || str_contains($pesan, 'Illegal mix of collations')) {
                $item = ItemBarang::where('kode_item', $kode)->first();
                if (! $item) {
                    return redirect()->route('admin.barang')->with('error', 'Barang tidak ditemukan.');
                }

                $nama = $item->nama_item;
                $item->delete();

                DB::table('log_aktivitas')->insert([
                    'waktu' => now(),
                    'keterangan' => 'Hapus item: ' . $kode . ' - ' . $nama,
                    'tipe_aksi' => 'ITEM_DIHAPUS',
                    'id_pengguna' => $idPengguna,
                    'data_sebelum' => null,
                    'data_sesudah' => null,
                ]);
            } else {
                return redirect()->route('admin.barang')
                    ->with('error', $this->terjemahkanError($e));
            }
        }

        return redirect()->route('admin.barang')->with('sukses', 'Barang berhasil dihapus.');
    }

    /**
     * Menerjemahkan pesan error database dari stored procedure ke bahasa Indonesia sederhana.
     */
    protected function terjemahkanError(Throwable $e): string
    {
        $pesan = $e->getMessage();

        if (str_contains($pesan, 'Kode item sudah dipakai')) {
            return 'Kode barang sudah dipakai.';
        }

        if (str_contains($pesan, 'riwayat transaksi') || str_contains($pesan, 'sudah memiliki riwayat transaksi')) {
            return 'Barang tidak bisa dihapus karena sudah pernah dijual.';
        }

        if (str_contains($pesan, 'Item tidak ditemukan')) {
            return 'Barang tidak ditemukan.';
        }

        Log::error('Kesalahan query database kelola barang', [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
        ]);

        return 'Terjadi kesalahan. Coba lagi.';
    }

    /**
     * Menampilkan riwayat transaksi contoh (Figma frame P12).
     */
    public function riwayat(): View
    {
        $transaksi = [
            (object) [
                'tanggal' => '03 Okt 2026, 14:20',
                'no_transaksi' => 'TRX-001',
                'kasir' => 'Rina',
                'items' => 'Kertas HVS A4 (2 rim), Pulpen Snowman (3 pcs)',
                'total' => 125000,
                'metode' => 'Tunai',
                'status' => 'Selesai',
            ],
            (object) [
                'tanggal' => '03 Okt 2026, 13:45',
                'no_transaksi' => 'TRX-002',
                'kasir' => 'Deni',
                'items' => 'Buku Tulis Sinar Dunia 38 Lembar (5 pcs)',
                'total' => 25000,
                'metode' => 'QRIS',
                'status' => 'Selesai',
            ],
            (object) [
                'tanggal' => '02 Okt 2026, 16:10',
                'no_transaksi' => 'TRX-003',
                'kasir' => 'Siti',
                'items' => 'Spidol Whiteboard Snowman (2 pcs), Penghapus Papan (1 pcs)',
                'total' => 32000,
                'metode' => 'Tunai',
                'status' => 'Selesai',
            ],
            (object) [
                'tanggal' => '02 Okt 2026, 11:30',
                'no_transaksi' => 'TRX-004',
                'kasir' => 'Rina',
                'items' => 'Map Snellhechter Plastik (10 pcs), Stopmap Folio (5 pcs)',
                'total' => 45000,
                'metode' => 'QRIS',
                'status' => 'Selesai',
            ],
            (object) [
                'tanggal' => '02 Okt 2026, 09:15',
                'no_transaksi' => 'TRX-005',
                'kasir' => 'Deni',
                'items' => 'Fotokopi KTP (20 lbr), Jilid Lakban (2 buku)',
                'total' => 18000,
                'metode' => 'Tunai',
                'status' => 'Selesai',
            ],
            (object) [
                'tanggal' => '01 Okt 2026, 15:50',
                'no_transaksi' => 'TRX-006',
                'kasir' => 'Siti',
                'items' => 'Kertas Buffalo Warna (1 rim), Lem Glukol (1 botol)',
                'total' => 65000,
                'metode' => 'Tunai',
                'status' => 'Selesai',
            ],
            (object) [
                'tanggal' => '01 Okt 2026, 13:05',
                'no_transaksi' => 'TRX-007',
                'kasir' => 'Rina',
                'items' => 'Tinta Stempel Yamura (1 botol), Bantalan Stempel (1 pcs)',
                'total' => 27500,
                'metode' => 'QRIS',
                'status' => 'Selesai',
            ],
            (object) [
                'tanggal' => '01 Okt 2026, 10:00',
                'no_transaksi' => 'TRX-008',
                'kasir' => 'Deni',
                'items' => 'Gunting Joyko Sedang (1 pcs), Penggaris Besi 30cm (1 pcs)',
                'total' => 22000,
                'metode' => 'Tunai',
                'status' => 'Selesai',
            ],
        ];

        return view('admin.barang.riwayat', [
            'transaksi' => $transaksi,
        ]);
    }
}
