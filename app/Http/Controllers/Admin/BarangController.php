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
        $stok = $tipe === 'JASA' ? 999999 : (int) ($request->validated('stok_awal') ?? 0);
        $batas = $request->validated('batas_minimum') !== null && $request->validated('batas_minimum') !== ''
            ? (int) $request->validated('batas_minimum')
            : 5;
        $idPengguna = Auth::id() ?? Auth::user()?->id_pengguna ?? 1;
        $kode = $request->validated('kode_item');
        $nama = $request->validated('nama_item');

        $gambarUrl = $request->validated('gambar_url');
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $dir = public_path('images/barang');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $ext = $file->getClientOriginalExtension() ?: 'webp';
            $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $kode) . '.' . $ext;
            $file->move($dir, $filename);
            $gambarUrl = '/images/barang/' . $filename;
        }

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
                    'link_shopee' => $request->validated('link_shopee'),
                    'deskripsi_produk' => $request->validated('deskripsi_produk'),
                    'gambar_url' => $gambarUrl,
                    'foto' => $gambarUrl,
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

        ItemBarang::where('kode_item', $kode)->update([
            'link_shopee' => $request->validated('link_shopee'),
            'deskripsi_produk' => $request->validated('deskripsi_produk'),
            'gambar_url' => $gambarUrl,
            'foto' => $gambarUrl,
            'stok' => $stok,
        ]);

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

        $gambarUrl = $request->validated('gambar_url');
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $dir = public_path('images/barang');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $ext = $file->getClientOriginalExtension() ?: 'webp';
            $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $kode) . '.' . $ext;
            $file->move($dir, $filename);
            $gambarUrl = '/images/barang/' . $filename;
        }

        $updatePayload = [
            'deskripsi_produk' => $request->validated('deskripsi_produk'),
        ];
        if ($request->has('link_shopee')) {
            $updatePayload['link_shopee'] = $request->validated('link_shopee');
        }
        if ($gambarUrl !== null) {
            $updatePayload['gambar_url'] = $gambarUrl;
            $updatePayload['foto'] = $gambarUrl;
        }
        if ($tipe === 'JASA') {
            $updatePayload['stok'] = 999999;
        }

        ItemBarang::where('kode_item', $kode)->update($updatePayload);

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
     * Helper query untuk riwayat transaksi barang dari database riil.
     */
    protected function getRiwayatQuery(Request $request)
    {
        $query = DB::table('daftar_penjualan as dp')
            ->leftJoin('pengguna as p', 'dp.id_kasir', '=', 'p.id_pengguna');

        if ($request->filled('kasir') && $request->input('kasir') !== 'Semua') {
            $kasir = $request->input('kasir');
            $query->where(function ($q) use ($kasir) {
                $q->where('p.nama', $kasir)
                  ->orWhere('p.username', $kasir)
                  ->orWhere('dp.id_kasir', $kasir);
            });
        }

        if ($request->filled('mulai')) {
            $query->whereDate('dp.tanggal_transaksi', '>=', $request->input('mulai'));
        }

        if ($request->filled('sampai')) {
            $query->whereDate('dp.tanggal_transaksi', '<=', $request->input('sampai'));
        }

        if ($request->filled('kode_item')) {
            $kodeItem = $request->input('kode_item');
            $query->whereExists(function ($sub) use ($kodeItem) {
                $sub->select(DB::raw(1))
                    ->from('daftar_transaksi as dt')
                    ->whereColumn('dt.kode_penjualan', 'dp.kode_transaksi')
                    ->where('dt.kode_item', $kodeItem);
            });
        }

        return $query->orderByDesc('dp.tanggal_transaksi')
            ->orderByDesc('dp.kode_transaksi')
            ->select([
                'dp.kode_transaksi',
                'dp.tanggal_transaksi',
                'dp.total_bayar',
                'dp.metode_bayar',
                'p.nama as nama_kasir',
            ]);
    }

    /**
     * Menampilkan riwayat transaksi riil dari database (tabel daftar_penjualan & daftar_transaksi).
     */
    public function riwayat(Request $request): View
    {
        $penjualanList = $this->getRiwayatQuery($request)->paginate(15)->withQueryString();

        $kodeList = $penjualanList->pluck('kode_transaksi');
        $itemsGrouped = DB::table('daftar_transaksi as dt')
            ->join('daftar_item as di', 'dt.kode_item', '=', 'di.kode_item')
            ->whereIn('dt.kode_penjualan', $kodeList)
            ->select('dt.kode_penjualan', 'di.nama_item', 'dt.item_keluar', 'di.satuan')
            ->get()
            ->groupBy('kode_penjualan');

        $transaksi = $penjualanList->getCollection()->map(function ($row) use ($itemsGrouped) {
            $items = $itemsGrouped->get($row->kode_transaksi, collect([]));
            $itemStrings = $items->map(function ($it) {
                $satuan = $it->satuan ?: 'pcs';
                return "{$it->nama_item} ({$it->item_keluar} {$satuan})";
            })->implode(', ');

            return (object) [
                'tanggal' => date('d M Y', strtotime($row->tanggal_transaksi)),
                'no_transaksi' => $row->kode_transaksi,
                'kasir' => $row->nama_kasir ?: 'Kasir',
                'items' => $itemStrings ?: 'Transaksi Penjualan',
                'total' => (float) $row->total_bayar,
                'metode' => $row->metode_bayar ?: 'Tunai',
                'status' => 'Selesai',
            ];
        });

        // Ambil daftar kasir riil dari database
        $daftarKasir = DB::table('pengguna')
            ->whereIn('role', ['KASIR', 'ADMIN'])
            ->orderBy('nama')
            ->get();

        return view('admin.barang.riwayat', [
            'transaksi' => $transaksi,
            'paginator' => $penjualanList,
            'daftarKasir' => $daftarKasir,
            'filterKasir' => $request->query('kasir', 'Semua'),
            'filterMulai' => $request->query('mulai'),
            'filterSampai' => $request->query('sampai'),
        ]);
    }

    /**
     * Mengekspor data riwayat transaksi riil ke CSV.
     */
    public function eksporRiwayat(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $penjualanList = $this->getRiwayatQuery($request)->get();
        $kodeList = $penjualanList->pluck('kode_transaksi');
        $itemsGrouped = DB::table('daftar_transaksi as dt')
            ->join('daftar_item as di', 'dt.kode_item', '=', 'di.kode_item')
            ->whereIn('dt.kode_penjualan', $kodeList)
            ->select('dt.kode_penjualan', 'di.nama_item', 'dt.item_keluar', 'di.satuan')
            ->get()
            ->groupBy('kode_penjualan');

        $neutralize = function (string $val): string {
            if ($val !== '' && in_array($val[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
                $val = "'" . $val;
            }
            if (str_contains($val, ',') || str_contains($val, '"') || str_contains($val, "\n") || str_contains($val, "\r")) {
                return '"' . str_replace('"', '""', $val) . '"';
            }
            return $val;
        };

        $lines = ["Tanggal,No. Transaksi,Kasir,Items,Total,Metode,Status"];
        foreach ($penjualanList as $row) {
            $items = $itemsGrouped->get($row->kode_transaksi, collect([]));
            $itemStrings = $items->map(function ($it) {
                $satuan = $it->satuan ?: 'pcs';
                return "{$it->nama_item} ({$it->item_keluar} {$satuan})";
            })->implode('; ');

            $tgl = $neutralize(date('d M Y', strtotime($row->tanggal_transaksi)));
            $no = $neutralize((string) $row->kode_transaksi);
            $ksr = $neutralize((string) ($row->nama_kasir ?: 'Kasir'));
            $itm = $neutralize($itemStrings ?: 'Transaksi Penjualan');
            $tot = (float) $row->total_bayar;
            $mtd = $neutralize((string) ($row->metode_bayar ?: 'Tunai'));
            $sts = 'Selesai';

            $lines[] = "{$tgl},{$no},{$ksr},{$itm},{$tot},{$mtd},{$sts}";
        }

        $csv = implode("\r\n", $lines) . "\r\n";
        $filename = 'riwayat-transaksi-barang-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
