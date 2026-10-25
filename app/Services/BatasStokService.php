<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BatasStokService
{
    /**
     * Mengambil daftar item terpaginasi dengan filter jenis dan pencarian kata kunci.
     */
    public function getItems(?string $jenis = null, ?string $keyword = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = DB::table('daftar_item');

        if (!empty($jenis)) {
            $query->where('jenis', $jenis);
        }

        if (!empty($keyword)) {
            $sanitized = trim($keyword);
            $query->where(function ($q) use ($sanitized) {
                $q->where('nama_item', 'like', "%{$sanitized}%")
                  ->orWhere('kode_item', 'like', "%{$sanitized}%");
            });
        }

        $query->orderBy('kode_item', 'asc');

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->setCollection($paginator->getCollection()->map(function ($row) {
            $stok = (int) ($row->stok ?? 0);
            $batas = (int) ($row->batas_minimum ?? 0);

            if ($stok <= 0) {
                $status = 'Habis';
            } elseif ($stok <= $batas) {
                $status = 'Menipis';
            } elseif ($stok <= $batas * 2) {
                $status = 'Normal';
            } else {
                $status = 'Aman';
            }

            // Pasok properti kompatibilitas view dan standar DB
            $row->kode = $row->kode_item;
            $row->nama = $row->nama_item;
            $row->satuan = $row->satuan ?: 'pcs';
            $row->batas = $batas;
            $row->status = $status;

            return $row;
        }));

        return $paginator;
    }

    /**
     * Mengambil daftar unik jenis item untuk opsi dropdown filter.
     */
    public function getJenisList(): array
    {
        return DB::table('daftar_item')
            ->whereNotNull('jenis')
            ->where('jenis', '!=', '')
            ->distinct()
            ->orderBy('jenis', 'asc')
            ->pluck('jenis')
            ->toArray();
    }

    /**
     * Memperbarui batas stok minimum item tertentu via Stored Procedure.
     */
    public function updateBatasItem(string $kodeItem, int $nilai, ?int $idPelaku = null): bool
    {
        if ($nilai < 0) {
            throw new InvalidArgumentException('Batas minimum tidak boleh negatif.');
        }

        DB::statement('CALL sp_set_batas_minimum(?, ?, ?)', [
            $kodeItem,
            $nilai,
            $idPelaku,
        ]);

        return true;
    }

    /**
     * Memperbarui batas stok minimum semua item non-jasa secara massal via Stored Procedure.
     */
    public function updateBatasSemua(int $nilai, ?int $idPelaku = null): bool
    {
        if ($nilai < 0) {
            throw new InvalidArgumentException('Batas minimum tidak boleh negatif.');
        }

        DB::statement('CALL sp_set_batas_minimum_semua(?, ?)', [
            $nilai,
            $idPelaku,
        ]);

        return true;
    }
}

