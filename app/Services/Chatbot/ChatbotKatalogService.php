<?php

namespace App\Services\Chatbot;

use App\Models\ItemBarang;
use Illuminate\Support\Facades\DB;

class ChatbotKatalogService
{
    /**
     * Mengambil konteks data barang publik terenkapsulasi untuk memori AI.
     * Mengunci akses HANYA pada 9 atribut resmi publik.
     */
    public function getPublicContext(string $keyword): string
    {
        $stopwords = [
            'dan', 'ada', 'apa', 'aja', 'saja', 'mau', 'bisa', 'nih', 'min', 'gan',
            'kak', 'toko', 'yang', 'dari', 'pada', 'atau', 'buat', 'jual', 'beli',
            'info', 'cari', 'dong', 'kah', 'pun', 'kok', 'sih', 'nya', 'deh', 'apakah',
            'berapa', 'disini', 'kamu', 'punya', 'tolong', 'halo', 'selamat', 'pagi', 'siang', 'sore', 'malam'
        ];

        $tokens = collect(preg_split('/\s+/', mb_strtolower($keyword)))
            ->map(fn ($k) => trim($k, " \t\n\r\0\x0B.,?!;:'\"()[]{}"))
            ->filter(fn ($k) => mb_strlen($k) >= 3 && !in_array($k, $stopwords))
            ->take(6);

        // ENKAPSULASI: HANYA PILIH ATRIBUT RESMI PUBLIK (9 Atribut)
        $query = ItemBarang::query()->select(
            'kode_item',
            'nama_item',
            'jenis',
            'merek',
            'satuan',
            'harga_jual',
            'deskripsi_produk',
            'link_shopee',
            DB::raw('stok as stok_fisik'),
            DB::raw('tipe_item as tipe_jasa')
        );

        $pdo = DB::getPdo();
        $andConditions = $tokens->map(fn ($t) => "nama_item LIKE " . $pdo->quote("%{$t}%"))->implode(' AND ');
        $orConditions  = $tokens->map(fn ($t) => "nama_item LIKE " . $pdo->quote("%{$t}%"))->implode(' OR ');

        $items = (clone $query)->where(function ($q) use ($tokens) {
            foreach ($tokens as $token) {
                $q->orWhere('nama_item', 'like', "%{$token}%")
                  ->orWhere('merek', 'like', "%{$token}%")
                  ->orWhere('jenis', 'like', "%{$token}%");
            }
        });

        if ($tokens->isNotEmpty()) {
            if ($tokens->count() > 1) {
                $items = $items->orderByRaw("
                    CASE 
                        WHEN ({$andConditions}) THEN 0
                        WHEN ({$orConditions}) THEN 1
                        ELSE 2
                    END, nama_item ASC
                ");
            } else {
                $items = $items->orderByRaw("
                    CASE 
                        WHEN ({$orConditions}) THEN 0
                        ELSE 1
                    END, nama_item ASC
                ");
            }
        } else {
            $items = $items->orderBy('nama_item', 'asc');
        }

        $items = $items->limit(25)->get();

        if ($items->isEmpty()) {
            if ($tokens->isNotEmpty()) {
                $firstToken = $tokens->first();
                $items = (clone $query)->where('nama_item', 'like', "%{$firstToken}%")
                    ->orWhere('jenis', 'like', "%{$firstToken}%")
                    ->limit(5)
                    ->get();
            }
        }

        if ($items->isEmpty()) {
            return "(Tidak ada barang yang cocok dengan kata kunci pencarian di katalog)";
        }

        return $items->map(function ($item) {
            $isJasa = $item->tipe_jasa == 1 || strtoupper((string) $item->tipe_jasa) === 'JASA';

            if ($isJasa) {
                $statusStok = 'Layanan tersedia';
            } elseif ($item->stok_fisik <= 0) {
                $statusStok = 'Habis';
            } elseif ($item->stok_fisik <= 3) {
                $statusStok = "Menipis (sisa {$item->stok_fisik})";
            } else {
                $statusStok = 'Tersedia';
            }

            $hargaRp = 'Rp ' . number_format((float) $item->harga_jual, 0, ',', '.');
            $shopeeInfo = !empty($item->link_shopee) ? " | Shopee: {$item->link_shopee}" : '';

            // Sanitasi & pembatasan deskripsi_produk
            $deskripsi = '';
            if (!empty($item->deskripsi_produk)) {
                $cleanDesc = str_replace(["\r", "\n", "\t", '|'], ' ', (string) $item->deskripsi_produk);
                $cleanDesc = trim(preg_replace('/\s+/', ' ', $cleanDesc));
                if ($cleanDesc !== '') {
                    $deskripsi = ' | Ket: ' . mb_substr($cleanDesc, 0, 80);
                }
            }

            return "{$item->kode_item} | {$item->nama_item} | {$item->jenis} | {$item->merek} | {$item->satuan} | {$hargaRp} | {$statusStok}{$shopeeInfo}{$deskripsi}";
        })->implode("\n");
    }
}
