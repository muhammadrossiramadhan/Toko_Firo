<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardKasirService
{
    public function ringkasan(): array
    {
        $user = Auth::user();

        return [
            'nama' => $user->name ?? $user->username ?? 'Kasir',
            'inisial' => strtoupper(substr(
                $user->name ?? $user->username ?? 'K',
                0,
                1
            )),
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
            ->select('kode_item', 'nama_item', 'stok')
            ->where('stok', '<=', 3)
            ->orderBy('stok')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->nama_item,
                    'sisa' => $item->stok,
                    'status' => $item->stok <= 0
                        ? 'Habis'
                        : ($item->stok <= 2 ? 'Menipis' : 'Rendah'),
                ];
            });
    }

    public function infoBarangBaru(): array
    {
        return DB::table('v_info_barang')
            ->orderByDesc('waktu')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'deskripsi' => $item->keterangan,
                    'waktu' => \Carbon\Carbon::parse(
                        $item->waktu
                    )->diffForHumans(),
                ];
        })
        ->toArray();
    }
}
