<?php

namespace App\Services\Telegram;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class TelegramReportService
{
    /**
     * Mengambil data ringkasan penjualan hari ini.
     */
    public function getSummaryData(): array
    {
        $hariIni = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');

        $transaksi = [
            (object) [
                'kode' => 'TRX-20261022-001',
                'kasir' => 'Siti Aminah',
                'item' => 'Buku Tulis Sinar Dunia 38L (10), Pulpen Pilot (5)',
                'qty' => 15,
                'subtotal' => 65000,
                'metode' => 'Tunai',
                'waktu' => '08:15',
            ],
            (object) [
                'kode' => 'TRX-20261022-002',
                'kasir' => 'Budi Santoso',
                'item' => 'Kertas HVS A4 70gr (2 Rim), Map Folio (10)',
                'qty' => 12,
                'subtotal' => 125000,
                'metode' => 'QRIS',
                'waktu' => '09:20',
            ],
            (object) [
                'kode' => 'TRX-20261022-003',
                'kasir' => 'Siti Aminah',
                'item' => 'Fotocopy Dokumen F4 (120 lbr), Jilid Lakban (2)',
                'qty' => 122,
                'subtotal' => 45000,
                'metode' => 'Tunai',
                'waktu' => '10:45',
            ],
            (object) [
                'kode' => 'TRX-20261022-004',
                'kasir' => 'Budi Santoso',
                'item' => 'Tinta Spidol Whiteboard (4), Penghapus Papan (1)',
                'qty' => 5,
                'subtotal' => 52000,
                'metode' => 'Tunai',
                'waktu' => '11:30',
            ],
            (object) [
                'kode' => 'TRX-20261022-005',
                'kasir' => 'Siti Aminah',
                'item' => 'Buku Tulis Sinar Dunia 38L (20), Pensil 2B (10)',
                'qty' => 30,
                'subtotal' => 110000,
                'metode' => 'QRIS',
                'waktu' => '13:10',
            ],
        ];

        return [
            'tanggal' => $hariIni,
            'total_penjualan' => 2450000,
            'jumlah_transaksi' => 14,
            'barang_terjual' => 85,
            'produk_terlaris' => 'Buku Tulis Sinar Dunia 38 Lembar',
            'saldo_kasir' => 3180000,
            'modal_awal' => 500000,
            'transaksi' => $transaksi,
        ];
    }

    /**
     * Menghasilkan binary PDF laporan penjualan menggunakan DomPDF.
     */
    public function generateSalesPdfBinary(?array $data = null): string
    {
        $data = $data ?? $this->getSummaryData();

        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'data' => $data,
        ])->setPaper('a4', 'portrait');

        return $pdf->output();
    }

    /**
     * Membangun teks caption ringkasan penjualan dengan format HTML Telegram.
     */
    public function buildSalesCaption(array $data): string
    {
        $totalPenjualan = number_format($data['total_penjualan'] ?? 0, 0, ',', '.');
        $saldoKasir = number_format($data['saldo_kasir'] ?? 0, 0, ',', '.');
        $transaksi = $data['jumlah_transaksi'] ?? 0;
        $barangTerjual = $data['barang_terjual'] ?? 0;
        $produkTerlaris = $data['produk_terlaris'] ?? '-';
        $tanggal = $data['tanggal'] ?? date('d/m/Y');

        return "📊 <b>LAPORAN PENJUALAN HARIAN TOKO FIRO</b>\n" .
               "📅 <b>Tanggal:</b> {$tanggal}\n\n" .
               "💰 <b>Total Penjualan:</b> Rp {$totalPenjualan}\n" .
               "🧾 <b>Jumlah Transaksi:</b> {$transaksi} nota\n" .
               "📦 <b>Barang Terjual:</b> {$barangTerjual} pcs\n" .
               "⭐ <b>Produk Terlaris:</b> {$produkTerlaris}\n" .
               "💵 <b>Saldo Kasir:</b> Rp {$saldoKasir}\n\n" .
               "<i>Dokumen rekap resmi cetak terlampir dalam berkas PDF di atas.</i>";
    }
}

