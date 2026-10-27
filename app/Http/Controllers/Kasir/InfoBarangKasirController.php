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
     * Menampilkan Info Barang Baru untuk kasir, dibaca dari view database v_info_barang.
     */
    public function index(Request $request): View
    {
        // Pemetaan tipe_aksi di v_info_barang -> tampilan Card
        $tampilanAksi = [
            'ITEM_BARU'    => ['tipe' => 'baru',  'badge' => 'Baru',   'inisial' => 'NEW', 'ket' => 'Ditambahkan ke katalog'],
            'HARGA_DIUBAH' => ['tipe' => 'harga', 'badge' => 'Harga',  'inisial' => 'Rp',  'ket' => 'Harga diperbarui'],
            'ITEM_DIUBAH'  => ['tipe' => 'stok',  'badge' => 'Update', 'inisial' => 'STK', 'ket' => 'Stok diperbarui oleh Admin'],
            'STOK_DIUBAH'  => ['tipe' => 'stok',  'badge' => 'Update', 'inisial' => 'STK', 'ket' => 'Stok diperbarui oleh Admin'],
        ];

        // Filter dari URL: ?tipe=baru|harga|stok (selain itu = Semua)
        $tipe = $request->query('tipe');
        if (! in_array($tipe, ['baru', 'harga', 'stok'], true)) {
            $tipe = null;
        }

        $log = DB::table('v_info_barang')
            ->when($tipe, function ($query) use ($tipe) {
                if ($tipe === 'baru') {
                    $query->where('tipe_aksi', 'ITEM_BARU');
                } elseif ($tipe === 'harga') {
                    $query->where('tipe_aksi', 'HARGA_DIUBAH');
                } elseif ($tipe === 'stok') {
                    $query->whereIn('tipe_aksi', ['ITEM_DIUBAH', 'STOK_DIUBAH']);
                }
            })
            ->orderByDesc('waktu')
            ->limit(50)
            ->get();

        $daftarPembaruan = $log->map(function ($row) use ($tampilanAksi) {
            $aksiKey = $row->tipe_aksi ?? 'ITEM_BARU';
            $p = $tampilanAksi[$aksiKey] ?? [
                'tipe' => 'stok',
                'badge' => 'Update',
                'inisial' => 'INFO',
                'ket' => 'Informasi produk diperbarui',
            ];

            // keterangan berformat "Awalan: KODE - Nama" atau teks langsung
            $rawKet = (string) ($row->keterangan ?? '');
            $kode = '';
            $nama = '';

            if (str_contains($rawKet, ': ')) {
                $sisa = Str::after($rawKet, ': ');
                if (str_contains($sisa, ' - ')) {
                    $kode = trim(Str::before($sisa, ' - '));
                    $nama = trim(Str::after($sisa, ' - '));
                } else {
                    $kode = trim($sisa);
                    $nama = trim($sisa);
                }
            } elseif (str_contains($rawKet, ' - ')) {
                $kode = trim(Str::before($rawKet, ' - '));
                $nama = trim(Str::after($rawKet, ' - '));
            } else {
                $nama = $rawKet !== '' ? $rawKet : 'Item Produk';
            }

            $item = $kode !== '' ? ItemBarang::where('kode_item', $kode)->first() : null;
            $dataSesudah = !empty($row->data_sesudah) ? json_decode((string) $row->data_sesudah, true) : null;

            if ($p['tipe'] === 'baru') {
                $harga = $item?->harga_jual ?? $dataSesudah['harga_jual'] ?? null;
                $stok = $item?->stok ?? $dataSesudah['stok'] ?? null;
                $detail = ($harga !== null && $stok !== null)
                    ? 'Harga: Rp ' . number_format((float) $harga, 0, ',', '.') . ' · Stok awal: ' . $stok
                    : 'Barang ditambahkan ke katalog';
            } elseif ($p['tipe'] === 'harga') {
                $harga = $item?->harga_jual ?? $dataSesudah['harga_jual'] ?? null;
                $detail = $harga !== null
                    ? 'Harga jual: Rp ' . number_format((float) $harga, 0, ',', '.')
                    : 'Harga jual diperbarui';
            } else {
                $stok = $item?->stok ?? $dataSesudah['stok'] ?? null;
                $detail = $stok !== null
                    ? 'Stok saat ini: ' . $stok
                    : 'Stok diperbarui';
            }

            $waktuFormatted = !empty($row->waktu)
                ? Carbon::parse($row->waktu)->locale('id')->diffForHumans()
                : '-';

            return [
                'tipe'       => $p['tipe'],
                'badge'      => $p['badge'],
                'inisial'    => $p['inisial'],
                'nama'       => $nama !== '' ? $nama : 'Item Produk',
                'keterangan' => $p['ket'],
                'detail'     => $detail,
                'waktu'      => $waktuFormatted,
                'id_pengguna'=> $row->id_pengguna ?? null,
                'tipe_aksi'  => $row->tipe_aksi ?? null,
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