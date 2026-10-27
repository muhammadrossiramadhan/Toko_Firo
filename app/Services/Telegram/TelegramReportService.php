<?php

namespace App\Services\Telegram;

use App\Services\LaporanPenjualanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TelegramReportService
{
    /**
     * Mengambil data ringkasan penjualan hari ini dari database riil.
     */
    public function getSummaryData(): array
    {
        $hariIni = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');
        $tglYmd = date('Y-m-d');

        $laporanService = app(LaporanPenjualanService::class);
        $ringkasan = $laporanService->ringkasan($tglYmd, $tglYmd);
        $transaksiPaginator = $laporanService->transaksi($tglYmd, $tglYmd, 50);

        return [
            'tanggal' => $hariIni,
            'total_penjualan' => $ringkasan['total_penjualan'] ?? 0,
            'jumlah_transaksi' => $ringkasan['jumlah_nota'] ?? 0,
            'barang_terjual' => $ringkasan['barang_terjual'] ?? 0,
            'produk_terlaris' => $ringkasan['barang_terlaris'] ?? '-',
            'laba_bersih' => $ringkasan['laba_bersih'] ?? 0,
            'transaksi' => $transaksiPaginator->items(),
        ];
    }

    /**
     * Menghasilkan binary PDF laporan penjualan untuk rentang tanggal tertentu atau data kustom.
     */
    public function generatePdfReport(?string $mulai = null, ?string $sampai = null, ?array $data = null): string
    {
        if ($data === null) {
            $mulai = $mulai ?? now()->toDateString();
            $sampai = $sampai ?? $mulai;
            $laporanService = app(\App\Services\LaporanPenjualanService::class);
            $ringkasan = $laporanService->ringkasan($mulai, $sampai);
            $transaksiList = $laporanService->transaksi($mulai, $sampai, 50);

            $transaksiItems = collect($transaksiList->items())->map(function ($t) {
                return (object) [
                    'kode'     => $t->kode_transaksi,
                    'kasir'    => $t->nama_kasir,
                    'item'     => ($t->jumlah_item ?? 1) . ' jenis item (' . ($t->total_qty ?? 1) . ' pcs)',
                    'qty'      => (int) ($t->total_qty ?? 1),
                    'subtotal' => (float) ($t->subtotal ?? 0),
                    'metode'   => 'Tunai',
                    'waktu'    => date('H:i', strtotime($t->tanggal)),
                ];
            })->all();

            $tanggalLabel = ($mulai === $sampai)
                ? Carbon::parse($mulai)->locale('id')->isoFormat('D MMMM Y')
                : Carbon::parse($mulai)->format('d/m/Y') . ' - ' . Carbon::parse($sampai)->format('d/m/Y');

            $data = [
                'tanggal'          => $tanggalLabel,
                'total_penjualan'  => (float) $ringkasan['total_penjualan'],
                'jumlah_transaksi' => (int) $ringkasan['jumlah_nota'],
                'barang_terjual'   => (int) $ringkasan['barang_terjual'],
                'produk_terlaris'  => $ringkasan['barang_terlaris'] ?: '-',
                'saldo_kasir'      => (float) $ringkasan['total_penjualan'],
                'modal_awal'       => 0,
                'transaksi'        => $transaksiItems,
            ];
        }

        return $this->generateSalesPdfBinary($data);
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
     * Menghasilkan binary PDF lembar restock barang butuh restock.
     */
    public function generateRestockPdfBinary(): string
    {
        $itemsKritis = DB::table('daftar_item')
            ->where(function ($q) {
                $q->where('tipe_item', '!=', 'JASA')
                  ->orWhereNull('tipe_item');
            })
            ->whereRaw('stok <= batas_minimum')
            ->orderBy('stok', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.lembar_restock', [
            'items' => $itemsKritis,
            'tanggal' => Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y'),
        ])->setPaper('a4', 'portrait');

        return $pdf->output();
    }

    /**
     * Membangun teks caption ringkasan penjualan dengan format HTML Telegram.
     */
    public function buildSalesCaption(array $data): string
    {
        $totalPenjualan = number_format($data['total_penjualan'] ?? 0, 0, ',', '.');
        $transaksi = $data['jumlah_transaksi'] ?? 0;
        $barangTerjual = $data['barang_terjual'] ?? 0;
        $produkTerlaris = $data['produk_terlaris'] ?? '-';
        $tanggal = $data['tanggal'] ?? date('d/m/Y');

        return "📊 <b>LAPORAN PENJUALAN HARIAN TOKO FIRO</b>\n" .
               "📅 <b>Tanggal:</b> {$tanggal}\n\n" .
               "💰 <b>Total Penjualan:</b> Rp {$totalPenjualan}\n" .
               "🧾 <b>Jumlah Transaksi:</b> {$transaksi} nota\n" .
               "📦 <b>Barang Terjual:</b> {$barangTerjual} pcs\n" .
               "⭐ <b>Produk Terlaris:</b> {$produkTerlaris}\n\n" .
               "<i>Dokumen rekap resmi cetak terlampir dalam berkas PDF di atas.</i>";
    }
}
