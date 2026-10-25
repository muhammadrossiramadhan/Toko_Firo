<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\ItemBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InfoBarangKasirController extends Controller
{
    /**
     * Menampilkan Info Barang Baru untuk kasir, dibaca dari tabel log_aktivitas.
     */
    public function index(Request $request): View
    {
        // Pemetaan tipe_aksi di log_aktivitas -> tampilan Card
        $tampilanAksi = [
            'ITEM_BARU'    => ['tipe' => 'baru',  'badge' => 'Baru',   'inisial' => 'NEW', 'ket' => 'Ditambahkan ke katalog'],
            'HARGA_DIUBAH' => ['tipe' => 'harga', 'badge' => 'Harga',  'inisial' => 'Rp',  'ket' => 'Harga diperbarui'],
            'STOK_DIUBAH'  => ['tipe' => 'stok',  'badge' => 'Update', 'inisial' => 'STK', 'ket' => 'Stok diperbarui oleh Admin'],
        ];

        // Filter dari URL: ?tipe=baru|harga|stok (selain itu = Semua)
        $tipe = $request->query('tipe');
        if (! in_array($tipe, ['baru', 'harga', 'stok'], true)) {
            $tipe = null;
        }

        $aksiDipakai = collect($tampilanAksi)
            ->filter(fn ($p) => $tipe === null || $p['tipe'] === $tipe)
            ->keys()
            ->all();

        $log = DB::table('log_aktivitas')
            ->whereIn('tipe_aksi', $aksiDipakai)
            ->orderByDesc('waktu')
            ->limit(50)
            ->get();

        $daftarPembaruan = $log->map(function ($row) use ($tampilanAksi) {
            $p = $tampilanAksi[$row->tipe_aksi];

            // keterangan berformat "Awalan: KODE - Nama"
            $sisa = Str::after($row->keterangan, ': ');
            $kode = trim(Str::before($sisa, ' - '));
            $nama = trim(Str::after($sisa, ' - '));

        $item = ItemBarang::where('kode_item', $kode)->first();

        if ($p['tipe'] === 'baru') {
            $detail = $item
                ? 'Harga: Rp ' . number_format($item->harga_jual, 0, ',', '.') . ' · Stok awal: ' . $item->stok
                : 'Barang ditambahkan ke katalog';
        } elseif ($p['tipe'] === 'harga') {
            $detail = $item
                ? 'Harga jual: Rp ' . number_format($item->harga_jual, 0, ',', '.')
                : 'Harga jual diperbarui';
        } else {
            $detail = $item
                ? 'Stok saat ini: ' . $item->stok
                : 'Stok diperbarui';
        }

            return [
                'tipe'       => $p['tipe'],
                'badge'      => $p['badge'],
                'inisial'    => $p['inisial'],
                'nama'       => $nama,
                'keterangan' => $p['ket'],
                'detail'     => $detail,
                'waktu'      => Carbon::parse($row->waktu)->locale('id')->diffForHumans(),
            ];
        });

        $kategori = ['Semua', 'Barang Baru', 'Perubahan Harga', 'Stok Diperbarui'];

        $user = auth()->user();
        $namaUser = $user->name ?? $user->nama ?? 'Kasir';
        $kasir = [
            'nama'    => $namaUser,
            'inisial' => strtoupper(substr($namaUser, 0, 1)),
            'role'    => 'Kasir',
        ];

        return view('kasir.info-barang.index', compact('kategori', 'daftarPembaruan', 'kasir', 'tipe'));
    }
}