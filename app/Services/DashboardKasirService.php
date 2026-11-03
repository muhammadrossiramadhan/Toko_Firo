<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardKasirService
{
    public function ringkasan(): array
    {
        $user = Auth::user();
        $nama = $user ? ($user->nama ?? $user->username ?? 'Kasir') : 'Kasir';

        return [
            'nama' => $nama,
            'inisial' => strtoupper(substr((string) $nama, 0, 1)),
            'role' => $user->role ?? 'Kasir',
            'tanggal' => now()->format('d-m-Y'),
        ];
    }

    public function saldoHariIni(): string
    {
        $saldo = DB::table('saldo_harian')
            ->where('tanggal', now()->toDateString())
            ->value('saldo_akhir');

        return 'Rp ' . number_format(
            (float) ($saldo ?? 0),
            0,
            ',',
            '.'
        );
    }

    public function stokMenipis()
    {
        return DB::table('daftar_item')
            ->select('kode_item', 'nama_item', 'stok', 'batas_minimum')
            ->whereRaw('stok <= COALESCE(batas_minimum, 5)')
            ->orderBy('stok')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $batas = (int) ($item->batas_minimum ?? 5);
                return [
                    'nama' => $item->nama_item,
                    'sisa' => $item->stok,
                    'status' => $item->stok <= 0
                        ? 'Habis'
                        : ($item->stok <= $batas ? 'Menipis' : 'Aman'),
                ];
            });
    }

    public function infoBarangBaru(): array
    {
        return DB::table('v_info_barang as vib')
            ->leftJoin('pengguna as p', 'vib.id_pengguna', '=', 'p.id_pengguna')
            ->select('vib.*', 'p.role as role_pelaku')
            ->orderByDesc('vib.waktu')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $role = strtoupper((string) ($item->role_pelaku ?? 'ADMIN'));
                $pelakuLabel = $role === 'KASIR' ? 'Kasir' : 'Admin';
                return [
                    'deskripsi' => $item->keterangan . " ({$pelakuLabel})",
                    'waktu' => \Carbon\Carbon::parse(
                        $item->waktu
                    )->diffForHumans(),
                ];
        })
        ->toArray();
    }
}
