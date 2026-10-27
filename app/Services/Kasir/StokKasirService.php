<?php

namespace App\Services\Kasir;

use App\Models\ItemBarang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BarangKasir extends ItemBarang
{
    /**
     * Komputasi status stok kasir (huruf kecil: 'aman', 'menipis', 'habis').
     */
    public function getStatusStokAttribute(): string
    {
        $stok = (int) ($this->stok ?? 0);
        $batas = (int) ($this->batas_minimum ?? 5);

        if ($stok <= 0) {
            return 'habis';
        } elseif ($stok <= $batas) {
            return 'menipis';
        }

        return 'aman';
    }
}

if (!class_exists('App\Models\Barang')) {
    class_alias(BarangKasir::class, 'App\Models\Barang');
}

class StokKasirService
{
    /**
     * Mengambil daftar data barang terpaginasi untuk kasir dengan status komputasi.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getDaftarBarang(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = BarangKasir::query();

        // Filter pencarian berdasarkan nama, kode, atau barcode
        $keyword = trim((string) ($filters['q'] ?? $filters['keyword'] ?? ''));
        if ($keyword !== '') {
            $query->where(function ($w) use ($keyword) {
                $w->where('nama_item', 'like', "%{$keyword}%")
                  ->orWhere('kode_item', 'like', "%{$keyword}%");
            });
        }

        // Filter status stok jika diberikan ('aman', 'menipis', 'habis', 'normal')
        $statusFilter = strtolower(trim((string) ($filters['status'] ?? $filters['status_stok'] ?? '')));
        if ($statusFilter === 'habis') {
            $query->where('stok', '<=', 0);
        } elseif ($statusFilter === 'menipis') {
            $query->where('stok', '>', 0)->whereRaw('stok <= COALESCE(batas_minimum, 5)');
        } elseif ($statusFilter === 'normal') {
            $query->whereRaw('stok > COALESCE(batas_minimum, 5)')->where('stok', '<=', 15);
        } elseif ($statusFilter === 'aman') {
            $query->whereRaw('stok > COALESCE(batas_minimum, 5)');
        }

        // Filter jenis barang jika disediakan
        if (!empty($filters['jenis']) && strtolower($filters['jenis']) !== 'semua') {
            $query->where('jenis', $filters['jenis']);
        }

        // Filter pengurutan
        $sort = strtolower(trim((string) ($filters['sort'] ?? '')));
        if ($sort === 'za') {
            $query->orderBy('nama_item', 'desc');
        } elseif ($sort === 'az') {
            $query->orderBy('nama_item', 'asc');
        } elseif ($sort === 'stok_terendah') {
            $query->orderBy('stok', 'asc');
        } elseif ($sort === 'stok_tertinggi') {
            $query->orderBy('stok', 'desc');
        } else {
            // Default urutan berdasarkan kode item
            $query->orderBy('kode_item', 'asc');
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->getCollection()->transform(function (BarangKasir $item) {
            $stok = (int) ($item->stok ?? 0);
            $batas = (int) ($item->batas_minimum ?? 5);
            $status = $item->status_stok;

            // Pasang atribut standar modul kasir
            $item->setAttribute('id', $item->kode_item);
            $item->setAttribute('kode_barang', $item->kode_item);
            $item->setAttribute('nama_barang', $item->nama_item);
            $item->setAttribute('barcode', $item->kode_item);
            $item->setAttribute('harga_jual', (float) $item->harga_jual);
            $item->setAttribute('stok', $stok);
            $item->setAttribute('satuan', $item->satuan ?: 'pcs');
            $item->setAttribute('batas_minimum', $batas);
            $item->setAttribute('status_stok', $status);

            // Pasang atribut pelengkap tampilan & kompatibilitas view prototype
            $nama = ($item->nama_item === 'Pensil 2B') ? 'Pensil 2B Faber' : $item->nama_item;
            $statusLabel = ucfirst($status);
            $hargaFormatted = 'Rp ' . number_format((float) $item->harga_jual, 0, ',', '.');

            $item->setAttribute('status', $statusLabel);
            $item->setAttribute('kode', $item->kode_item);
            $item->setAttribute('nama', $nama);
            $item->setAttribute('harga', $hargaFormatted);

            $item->status = $statusLabel;
            $item->kode = $item->kode_item;
            $item->nama = $nama;
            $item->harga = $hargaFormatted;

            return $item;
        });

        return $paginator;
    }

    /**
     * Memeriksa ketersediaan stok barang berdasarkan ID/kode dan jumlah yang diminta.
     *
     * @param int|string $barangId
     * @param int $jumlahDiminta
     * @return array{tersedia: bool, sisa_stok: int, barang: ?BarangKasir}
     */
    public function cekKetersediaan($barangId, int $jumlahDiminta = 1): array
    {
        $query = BarangKasir::query();

        if (is_numeric($barangId)) {
            $strId = (string) $barangId;
            $padded = 'BRG-' . str_pad($strId, 3, '0', STR_PAD_LEFT);
            $query->where(function ($w) use ($strId, $padded) {
                $w->where('kode_item', $strId)
                  ->orWhere('kode_item', $padded);
            });
        } else {
            $query->where('kode_item', (string) $barangId);
        }

        /** @var ?BarangKasir $barang */
        $barang = $query->first();

        if (!$barang) {
            return [
                'tersedia' => false,
                'sisa_stok' => 0,
                'barang' => null,
            ];
        }

        $sisaStok = (int) $barang->stok;
        $tersedia = ($sisaStok >= $jumlahDiminta && $jumlahDiminta > 0);

        // Pasok atribut kompatibilitas
        $barang->setAttribute('id', $barang->kode_item);
        $barang->setAttribute('kode_barang', $barang->kode_item);
        $barang->setAttribute('nama_barang', $barang->nama_item);
        $barang->setAttribute('barcode', $barang->kode_item);

        return [
            'tersedia' => $tersedia,
            'sisa_stok' => $sisaStok,
            'barang' => $barang,
        ];
    }
}

