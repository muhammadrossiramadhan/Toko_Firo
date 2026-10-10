<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DemoBarangController extends Controller
{
    public function index(Request $request)
    {
        // 47 data dummy dalam array statis (BRG-001 sampai BRG-047)
        $items = [
            [
                'kode' => 'BRG-001',
                'nama_item' => 'Pulpen Pilot G2',
                'jenis' => 'ATK',
                'merek' => 'Pilot',
                'rak' => 'A1',
                'satuan' => 'pcs',
                'harga_pokok' => 8000,
                'harga_jual' => 15000,
                'stok' => 24,
            ],
            [
                'kode' => 'BRG-002',
                'nama_item' => 'Kertas HVS A4 70gr',
                'jenis' => 'ATK',
                'merek' => 'Sinar Dunia',
                'rak' => 'B2',
                'satuan' => 'rim',
                'harga_pokok' => 42000,
                'harga_jual' => 52000,
                'stok' => 3,
            ],
            [
                'kode' => 'BRG-003',
                'nama_item' => 'Tinta Printer Canon',
                'jenis' => 'ATK',
                'merek' => 'Canon',
                'rak' => 'C1',
                'satuan' => 'botol',
                'harga_pokok' => 65000,
                'harga_jual' => 90000,
                'stok' => 1,
            ],
            [
                'kode' => 'BRG-004',
                'nama_item' => 'Buku Tulis Sidu 58hal',
                'jenis' => 'ATK',
                'merek' => 'Sidu',
                'rak' => 'A2',
                'satuan' => 'pcs',
                'harga_pokok' => 3500,
                'harga_jual' => 5500,
                'stok' => 40,
            ],
            [
                'kode' => 'BRG-005',
                'nama_item' => 'Fotocopy A4 B/W',
                'jenis' => 'Fotocopy',
                'merek' => '-',
                'rak' => '-',
                'satuan' => 'lembar',
                'harga_pokok' => 150,
                'harga_jual' => 300,
                'stok' => 999,
            ],
            [
                'kode' => 'BRG-006',
                'nama_item' => 'Isi Staples No.10',
                'jenis' => 'ATK',
                'merek' => 'Kangaro',
                'rak' => 'A3',
                'satuan' => 'box',
                'harga_pokok' => 2200,
                'harga_jual' => 4500,
                'stok' => 2,
            ],
            [
                'kode' => 'BRG-007',
                'nama_item' => 'Pensil 2B Faber',
                'jenis' => 'ATK',
                'merek' => 'Faber-Castell',
                'rak' => 'A1',
                'satuan' => 'pcs',
                'harga_pokok' => 3000,
                'harga_jual' => 5000,
                'stok' => 0,
            ],
            [
                'kode' => 'BRG-008',
                'nama_item' => 'Spidol Snowman Boardmarker',
                'jenis' => 'ATK',
                'merek' => 'Snowman',
                'rak' => 'A2',
                'satuan' => 'pcs',
                'harga_pokok' => 8500,
                'harga_jual' => 12000,
                'stok' => 15,
            ],
            [
                'kode' => 'BRG-009',
                'nama_item' => 'Lakban Coklat 2 Inch',
                'jenis' => 'ATK',
                'merek' => 'Daimaru',
                'rak' => 'B1',
                'satuan' => 'roll',
                'harga_pokok' => 11000,
                'harga_jual' => 16000,
                'stok' => 4,
            ],
            [
                'kode' => 'BRG-010',
                'nama_item' => 'Map Folio Kancing Plastik',
                'jenis' => 'ATK',
                'merek' => 'Big',
                'rak' => 'C2',
                'satuan' => 'pcs',
                'harga_pokok' => 3000,
                'harga_jual' => 4500,
                'stok' => 0,
            ],
            [
                'kode' => 'BRG-011',
                'nama_item' => 'Tip-Ex Kertas Correction Tape',
                'jenis' => 'ATK',
                'merek' => 'Joyko',
                'rak' => 'A3',
                'satuan' => 'pcs',
                'harga_pokok' => 6000,
                'harga_jual' => 9000,
                'stok' => 18,
            ],
            [
                'kode' => 'BRG-012',
                'nama_item' => 'Fotocopy F4 B/W',
                'jenis' => 'Fotocopy',
                'merek' => '-',
                'rak' => '-',
                'satuan' => 'lembar',
                'harga_pokok' => 180,
                'harga_jual' => 350,
                'stok' => 850,
            ],
            [
                'kode' => 'BRG-013',
                'nama_item' => 'Penggaris Besi 30cm',
                'jenis' => 'ATK',
                'merek' => 'Butterfly',
                'rak' => 'B3',
                'satuan' => 'pcs',
                'harga_pokok' => 5000,
                'harga_jual' => 8000,
                'stok' => 2,
            ],
            [
                'kode' => 'BRG-014',
                'nama_item' => 'Gunting Sedang Stainless',
                'jenis' => 'ATK',
                'merek' => 'Kenko',
                'rak' => 'B3',
                'satuan' => 'pcs',
                'harga_pokok' => 7500,
                'harga_jual' => 11000,
                'stok' => 12,
            ],
            [
                'kode' => 'BRG-015',
                'nama_item' => 'Lem Kertas Stick 8gr',
                'jenis' => 'ATK',
                'merek' => 'Glukol',
                'rak' => 'A2',
                'satuan' => 'pcs',
                'harga_pokok' => 2500,
                'harga_jual' => 4000,
                'stok' => 0,
            ],
            [
                'kode' => 'BRG-016',
                'nama_item' => 'Binder Clip No.107',
                'jenis' => 'ATK',
                'merek' => 'Joyko',
                'rak' => 'C1',
                'satuan' => 'box',
                'harga_pokok' => 4500,
                'harga_jual' => 7000,
                'stok' => 25,
            ],
            [
                'kode' => 'BRG-017',
                'nama_item' => 'Kertas HVS F4 70gr',
                'jenis' => 'ATK',
                'merek' => 'Sinar Dunia',
                'rak' => 'B2',
                'satuan' => 'rim',
                'harga_pokok' => 46000,
                'harga_jual' => 56000,
                'stok' => 8,
            ],
            [
                'kode' => 'BRG-018',
                'nama_item' => 'Buku Gambar A3',
                'jenis' => 'ATK',
                'merek' => 'Sidu',
                'rak' => 'A4',
                'satuan' => 'pcs',
                'harga_pokok' => 7000,
                'harga_jual' => 10000,
                'stok' => 3,
            ],
            [
                'kode' => 'BRG-019',
                'nama_item' => 'Spidol Permanen Hitam',
                'jenis' => 'ATK',
                'merek' => 'Snowman',
                'rak' => 'A2',
                'satuan' => 'pcs',
                'harga_pokok' => 8000,
                'harga_jual' => 11000,
                'stok' => 30,
            ],
            [
                'kode' => 'BRG-020',
                'nama_item' => 'Penghapus Pensil 4B',
                'jenis' => 'ATK',
                'merek' => 'Faber-Castell',
                'rak' => 'A1',
                'satuan' => 'pcs',
                'harga_pokok' => 2000,
                'harga_jual' => 3500,
                'stok' => 0,
            ],
            [
                'kode' => 'BRG-021',
                'nama_item' => 'Cetak Warna A4 (Tinta)',
                'jenis' => 'Fotocopy',
                'merek' => '-',
                'rak' => '-',
                'satuan' => 'lembar',
                'harga_pokok' => 1000,
                'harga_jual' => 2000,
                'stok' => 500,
            ],
            [
                'kode' => 'BRG-022',
                'nama_item' => 'Stapler HD-10',
                'jenis' => 'ATK',
                'merek' => 'Max',
                'rak' => 'A3',
                'satuan' => 'pcs',
                'harga_pokok' => 18000,
                'harga_jual' => 25000,
                'stok' => 7,
            ],
            [
                'kode' => 'BRG-023',
                'nama_item' => 'Cutter Kecil A-300',
                'jenis' => 'ATK',
                'merek' => 'Kenko',
                'rak' => 'B3',
                'satuan' => 'pcs',
                'harga_pokok' => 6500,
                'harga_jual' => 9500,
                'stok' => 1,
            ],
            [
                'kode' => 'BRG-024',
                'nama_item' => 'Isi Pisau Cutter A-100',
                'jenis' => 'ATK',
                'merek' => 'Kenko',
                'rak' => 'B3',
                'satuan' => 'tube',
                'harga_pokok' => 4000,
                'harga_jual' => 6000,
                'stok' => 14,
            ],
            [
                'kode' => 'BRG-025',
                'nama_item' => 'Kertas Buffalo Warna',
                'jenis' => 'ATK',
                'merek' => 'Sinar Dunia',
                'rak' => 'B1',
                'satuan' => 'pack',
                'harga_pokok' => 22000,
                'harga_jual' => 28000,
                'stok' => 6,
            ],
            [
                'kode' => 'BRG-026',
                'nama_item' => 'Amplop Coklat Folio Tali',
                'jenis' => 'ATK',
                'merek' => 'Paperline',
                'rak' => 'C3',
                'satuan' => 'pack',
                'harga_pokok' => 18000,
                'harga_jual' => 24000,
                'stok' => 0,
            ],
            [
                'kode' => 'BRG-027',
                'nama_item' => 'Map Snelhechter Plastik',
                'jenis' => 'ATK',
                'merek' => 'Joyko',
                'rak' => 'C2',
                'satuan' => 'pcs',
                'harga_pokok' => 3500,
                'harga_jual' => 5500,
                'stok' => 22,
            ],
            [
                'kode' => 'BRG-028',
                'nama_item' => 'Jilid Lakban Biasa',
                'jenis' => 'Fotocopy',
                'merek' => '-',
                'rak' => '-',
                'satuan' => 'buku',
                'harga_pokok' => 2500,
                'harga_jual' => 5000,
                'stok' => 100,
            ],
            [
                'kode' => 'BRG-029',
                'nama_item' => 'Jilid Spiral Kawat',
                'jenis' => 'Fotocopy',
                'merek' => '-',
                'rak' => '-',
                'satuan' => 'buku',
                'harga_pokok' => 7000,
                'harga_jual' => 12000,
                'stok' => 2,
            ],
            [
                'kode' => 'BRG-030',
                'nama_item' => 'Kalkulator 12 Digit',
                'jenis' => 'ATK',
                'merek' => 'Citizen',
                'rak' => 'D1',
                'satuan' => 'unit',
                'harga_pokok' => 45000,
                'harga_jual' => 65000,
                'stok' => 5,
            ],
            [
                'kode' => 'BRG-031',
                'nama_item' => 'Kertas Karton Manila Putih',
                'jenis' => 'ATK',
                'merek' => 'Paperline',
                'rak' => 'B1',
                'satuan' => 'lembar',
                'harga_pokok' => 2500,
                'harga_jual' => 4000,
                'stok' => 0,
            ],
            [
                'kode' => 'BRG-032',
                'nama_item' => 'Pulpen Gel 0.5 Hitam',
                'jenis' => 'ATK',
                'merek' => 'Joyko',
                'rak' => 'A1',
                'satuan' => 'pcs',
                'harga_pokok' => 3000,
                'harga_jual' => 5000,
                'stok' => 35,
            ],
            [
                'kode' => 'BRG-033',
                'nama_item' => 'Pensil Warna 12 Warna',
                'jenis' => 'ATK',
                'merek' => 'Faber-Castell',
                'rak' => 'A4',
                'satuan' => 'set',
                'harga_pokok' => 19000,
                'harga_jual' => 26000,
                'stok' => 4,
            ],
            [
                'kode' => 'BRG-034',
                'nama_item' => 'Clear Holder 20 Pocket',
                'jenis' => 'ATK',
                'merek' => 'Data Flex',
                'rak' => 'C2',
                'satuan' => 'pcs',
                'harga_pokok' => 12000,
                'harga_jual' => 17000,
                'stok' => 11,
            ],
            [
                'kode' => 'BRG-035',
                'nama_item' => 'Double Tape 1 Inch',
                'jenis' => 'ATK',
                'merek' => 'Nachi',
                'rak' => 'B1',
                'satuan' => 'roll',
                'harga_pokok' => 6000,
                'harga_jual' => 9000,
                'stok' => 16,
            ],
            [
                'kode' => 'BRG-036',
                'nama_item' => 'Lakban Bening 2 Inch',
                'jenis' => 'ATK',
                'merek' => 'Daimaru',
                'rak' => 'B1',
                'satuan' => 'roll',
                'harga_pokok' => 11000,
                'harga_jual' => 16000,
                'stok' => 0,
            ],
            [
                'kode' => 'BRG-037',
                'nama_item' => 'Buku Kas Folio 100 Lembar',
                'jenis' => 'ATK',
                'merek' => 'Paperline',
                'rak' => 'A4',
                'satuan' => 'buku',
                'harga_pokok' => 14000,
                'harga_jual' => 20000,
                'stok' => 9,
            ],
            [
                'kode' => 'BRG-038',
                'nama_item' => 'Laminating A4 Panas',
                'jenis' => 'Fotocopy',
                'merek' => '-',
                'rak' => '-',
                'satuan' => 'lembar',
                'harga_pokok' => 2000,
                'harga_jual' => 4000,
                'stok' => 250,
            ],
            [
                'kode' => 'BRG-039',
                'nama_item' => 'Laminating F4 Panas',
                'jenis' => 'Fotocopy',
                'merek' => '-',
                'rak' => '-',
                'satuan' => 'lembar',
                'harga_pokok' => 2500,
                'harga_jual' => 5000,
                'stok' => 180,
            ],
            [
                'kode' => 'BRG-040',
                'nama_item' => 'Tinta Stempel Biru',
                'jenis' => 'ATK',
                'merek' => 'Yamura',
                'rak' => 'C1',
                'satuan' => 'botol',
                'harga_pokok' => 5500,
                'harga_jual' => 8000,
                'stok' => 3,
            ],
            [
                'kode' => 'BRG-041',
                'nama_item' => 'Bak Stempel No.1',
                'jenis' => 'ATK',
                'merek' => 'Hero',
                'rak' => 'C1',
                'satuan' => 'pcs',
                'harga_pokok' => 8500,
                'harga_jual' => 12500,
                'stok' => 10,
            ],
            [
                'kode' => 'BRG-042',
                'nama_item' => 'Kertas HVS A4 80gr',
                'jenis' => 'ATK',
                'merek' => 'PaperOne',
                'rak' => 'B2',
                'satuan' => 'rim',
                'harga_pokok' => 48000,
                'harga_jual' => 59000,
                'stok' => 0,
            ],
            [
                'kode' => 'BRG-043',
                'nama_item' => 'Kertas HVS F4 80gr',
                'jenis' => 'ATK',
                'merek' => 'PaperOne',
                'rak' => 'B2',
                'satuan' => 'rim',
                'harga_pokok' => 52000,
                'harga_jual' => 64000,
                'stok' => 7,
            ],
            [
                'kode' => 'BRG-044',
                'nama_item' => 'Sticky Notes Post-It 3x3',
                'jenis' => 'ATK',
                'merek' => '3M',
                'rak' => 'A2',
                'satuan' => 'pad',
                'harga_pokok' => 7500,
                'harga_jual' => 11000,
                'stok' => 2,
            ],
            [
                'kode' => 'BRG-045',
                'nama_item' => 'Trigonal Clip No.3',
                'jenis' => 'ATK',
                'merek' => 'Joyko',
                'rak' => 'C1',
                'satuan' => 'box',
                'harga_pokok' => 2500,
                'harga_jual' => 4000,
                'stok' => 45,
            ],
            [
                'kode' => 'BRG-046',
                'nama_item' => 'Map Ordner Folio 7cm',
                'jenis' => 'ATK',
                'merek' => 'Bantex',
                'rak' => 'D2',
                'satuan' => 'pcs',
                'harga_pokok' => 24000,
                'harga_jual' => 32000,
                'stok' => 8,
            ],
            [
                'kode' => 'BRG-047',
                'nama_item' => 'Cetak Foto Glossy 4R',
                'jenis' => 'Fotocopy',
                'merek' => '-',
                'rak' => '-',
                'satuan' => 'lembar',
                'harga_pokok' => 1500,
                'harga_jual' => 3500,
                'stok' => 0,
            ],
        ];

        // Status dihitung dari stok: 0 = Habis, di bawah 5 = Menipis, selain itu = Aman
        foreach ($items as &$item) {
            if ($item['stok'] === 0) {
                $item['status'] = 'Habis';
            } elseif ($item['stok'] < 5) {
                $item['status'] = 'Menipis';
            } else {
                $item['status'] = 'Aman';
            }
        }
        unset($item);

        // Paginasi: 7 per halaman, total 7 halaman dari 47 item
        $perPage = 7;
        $total = count($items);
        $totalPages = (int) ceil($total / $perPage);
        $currentPage = max(1, min((int) $request->input('page', 1), $totalPages > 0 ? $totalPages : 1));

        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = array_slice($items, $offset, $perPage);

        $from = $total > 0 ? $offset + 1 : 0;
        $to = min($offset + $perPage, $total);

        // Susunan tombol halaman sesuai prototype: Sebelumnya, 1, 2, 3, 5, ..., Selanjutnya
        $pageElements = $this->generatePageElements($currentPage, $totalPages);

        return view('demo.barang', [
            'items' => $paginatedItems,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'total' => $total,
            'from' => $from,
            'to' => $to,
            'pageElements' => $pageElements,
        ]);
    }

    /**
     * Menghasilkan susunan tombol halaman sesuai prototype desain.
     */
    private function generatePageElements(int $currentPage, int $totalPages): array
    {
        if ($totalPages <= 1) {
            return [1];
        }

        if ($totalPages <= 5) {
            return range(1, $totalPages);
        }

        // Tampilan halaman 1 dan 2 menampilkan pola [1, 2, 3, '...', 5, '...', $totalPages]
        if ($currentPage <= 2) {
            return [1, 2, 3, '...', 5, '...', $totalPages];
        }

        if ($currentPage === 3) {
            return [1, 2, 3, 4, 5, '...', $totalPages];
        }

        if ($currentPage === 4) {
            return [1, 2, 3, 4, 5, 6, $totalPages];
        }

        if ($currentPage >= 5) {
            return [1, '...', 3, 4, 5, 6, $totalPages];
        }

        return range(1, $totalPages);
    }
}
