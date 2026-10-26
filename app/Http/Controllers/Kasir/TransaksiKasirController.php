<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kasir\SimpanTransaksiRequest;
use App\Models\DaftarPenjualan;
use App\Models\ItemBarang;
use App\Services\Kasir\TransaksiKasirService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class TransaksiKasirController extends Controller
{
    public function __construct(
        protected TransaksiKasirService $transaksiKasirService
    ) {}

    /**
     * Menampilkan antarmuka POS Transaksi Penjualan Kasir.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        // Proteksi autentikasi untuk rute kasir.transaksi.index
        if ($request->routeIs('kasir.transaksi.index') && !auth()->check()) {
            return redirect()->guest(route('login'));
        }

        $items = ItemBarang::query()->limit(15)->get();

        if ($items->isEmpty()) {
            // Mock data fallback untuk prototype UI & testing independen
            $katalog = [
                [
                    'kode' => 'BRG-002',
                    'nama' => 'Kertas HVS A4 70gr',
                    'harga' => 52000,
                    'stok' => 3,
                    'satuan' => 'rim',
                ],
                [
                    'kode' => 'BRG-003',
                    'nama' => 'Pulpen Pilot G2',
                    'harga' => 15000,
                    'stok' => 24,
                    'satuan' => 'pcs',
                ],
                [
                    'kode' => 'BRG-004',
                    'nama' => 'Buku Tulis Sidu 58hal',
                    'harga' => 5500,
                    'stok' => 40,
                    'satuan' => 'buku',
                ],
                [
                    'kode' => 'BRG-005',
                    'nama' => 'Spidol Snowman Hitam',
                    'harga' => 8000,
                    'stok' => 12,
                    'satuan' => 'pcs',
                ],
                [
                    'kode' => 'BRG-006',
                    'nama' => 'Isi Staples No.10',
                    'harga' => 4500,
                    'stok' => 2,
                    'satuan' => 'kotak',
                ],
                [
                    'kode' => 'BRG-007',
                    'nama' => 'Map Plastik F4',
                    'harga' => 3000,
                    'stok' => 35,
                    'satuan' => 'lembar',
                ],
            ];
            $keranjang = [
                [
                    'kode' => 'BRG-002',
                    'nama' => 'Kertas HVS A4 70gr',
                    'harga' => 52000,
                    'qty' => 1,
                    'subtotal' => 52000,
                ],
                [
                    'kode' => 'BRG-003',
                    'nama' => 'Pulpen Pilot G2',
                    'harga' => 15000,
                    'qty' => 2,
                    'subtotal' => 30000,
                ],
            ];
            $totalBelanja = 82000;
        } else {
            $katalog = $items->map(function ($it) {
                return [
                    'kode' => $it->kode_item,
                    'nama' => $it->nama_item,
                    'harga' => (float) $it->harga_jual,
                    'stok' => (int) $it->stok,
                    'satuan' => $it->satuan ?: 'pcs',
                ];
            })->toArray();
            $keranjang = [];
            $totalBelanja = 0;
        }

        $user = auth()->user();
        $kasir = [
            'nama' => $user ? ($user->name ?? $user->nama ?? 'Kasir') : 'Rina Putri',
            'inisial' => strtoupper(substr((string) ($user ? ($user->name ?? $user->nama ?? 'Kasir') : 'R'), 0, 1)),
            'role' => 'Kasir',
        ];

        return view('kasir.transaksi.index', compact('katalog', 'keranjang', 'totalBelanja', 'kasir'));
    }

    /**
     * Memproses penyimpanan transaksi penjualan POS kasir.
     */
    public function simpan(SimpanTransaksiRequest $request): JsonResponse
    {
        try {
            $kasirId = (int) (auth()->id() ?? auth()->user()?->id_pengguna ?? 1);
            $transaksi = $this->transaksiKasirService->prosesTransaksi($request->validated(), $kasirId);

            $nomorNota = $transaksi->kode_transaksi;
            $kembalian = (float) $transaksi->kembalian;
            $urlCetak = route('kasir.transaksi.cetak', ['nota' => $nomorNota]);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi penjualan berhasil disimpan.',
                'nomor_nota' => $nomorNota,
                'kembalian' => $kembalian,
                'url_cetak' => $urlCetak,
                'data' => $transaksi,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Mengambil detail data transaksi berdasarkan nomor nota.
     */
    public function detail(string $nota): JsonResponse|View
    {
        $transaksi = $this->transaksiKasirService->getDetailTransaksi($nota);

        if (!$transaksi) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi penjualan tidak ditemukan.',
                ], 404);
            }
            abort(404, 'Transaksi penjualan tidak ditemukan.');
        }

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $transaksi,
            ], 200);
        }

        if (view()->exists('kasir.transaksi.detail')) {
            return view('kasir.transaksi.detail', compact('transaksi'));
        }

        return response()->json([
            'status' => 'success',
            'data' => $transaksi,
        ], 200);
    }

    /**
     * Menampilkan struk/resi transaksi penjualan kasir siap cetak.
     */
    public function cetakStruk(string $nota): Response|View
    {
        $transaksi = $this->transaksiKasirService->getDetailTransaksi($nota);

        if (!$transaksi) {
            abort(404, 'Transaksi penjualan tidak ditemukan.');
        }

        if (view()->exists('kasir.transaksi.cetak')) {
            return view('kasir.transaksi.cetak', compact('transaksi'));
        }

        return response($this->renderStrukHtml($transaksi), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    /**
     * Render HTML struk thermal ramah printer, patuh aturan K12 (bebas SVG/emoji) dan K18.
     */
    protected function renderStrukHtml(DaftarPenjualan $transaksi): string
    {
        $tanggal = $transaksi->tanggal_transaksi ? date('d/m/Y', strtotime((string) $transaksi->tanggal_transaksi)) : date('d/m/Y');
        $pelanggan = htmlspecialchars((string) ($transaksi->nama_pelanggan ?: 'Umum'));
        $kasirNama = htmlspecialchars((string) ($transaksi->kasir->nama ?? 'Kasir Toko Firo'));
        $totalBayar = number_format((float) $transaksi->total_bayar, 0, ',', '.');
        $metode = htmlspecialchars((string) ($transaksi->metode_bayar ?: 'TUNAI'));

        $itemRows = '';
        foreach ($transaksi->transaksi as $detail) {
            $namaItem = htmlspecialchars((string) ($detail->item->nama_item ?? $detail->kode_item));
            $qty = (int) ($detail->item_keluar ?: $detail->jumlah_item ?: 1);
            $subtotal = number_format((float) $detail->subtotal, 0, ',', '.');
            $hargaSatuan = $qty > 0 ? number_format((float) $detail->subtotal / $qty, 0, ',', '.') : $subtotal;

            $itemRows .= "
                <tr>
                    <td style='padding: 4px 0;' colspan='2'><strong>{$namaItem}</strong></td>
                </tr>
                <tr>
                    <td style='padding: 0 0 6px 0; color: #555;'>{$qty} x Rp {$hargaSatuan}</td>
                    <td style='padding: 0 0 6px 0; text-align: right;'>Rp {$subtotal}</td>
                </tr>
            ";
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Pembelian - {$transaksi->kode_transaksi}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;
            color: #111;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .struk-card {
            max-width: 380px;
            margin: 0 auto;
            background: #fff;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .header { text-align: center; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: bold; }
        .header p { margin: 4px 0; font-size: 14px; color: #444; }
        .divider { border-top: 1px dashed #777; margin: 12px 0; }
        .info-table, .item-table, .total-table { width: 100%; border-collapse: collapse; font-size: 15px; }
        .text-right { text-align: right; }
        .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #444; }
        .btn-print {
            display: block;
            width: 100%;
            min-height: 44px;
            background: #059669;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .struk-card { box-shadow: none; padding: 0; max-width: 100%; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="struk-card">
        <div class="header">
            <h1>TOKO FIRO</h1>
            <p>Alat Tulis Kantor &amp; Jasa Fotocopy</p>
            <p>Telepon: 0812-3456-7890</p>
        </div>

        <div class="divider"></div>

        <table class="info-table">
            <tr><td>Nota:</td><td class="text-right"><strong>{$transaksi->kode_transaksi}</strong></td></tr>
            <tr><td>Tanggal:</td><td class="text-right">{$tanggal}</td></tr>
            <tr><td>Kasir:</td><td class="text-right">{$kasirNama}</td></tr>
            <tr><td>Pelanggan:</td><td class="text-right">{$pelanggan}</td></tr>
        </table>

        <div class="divider"></div>

        <table class="item-table">
            {$itemRows}
        </table>

        <div class="divider"></div>

        <table class="total-table">
            <tr>
                <td style="font-size: 18px; font-weight: bold;">TOTAL BAYAR:</td>
                <td class="text-right" style="font-size: 18px; font-weight: bold;">Rp {$totalBayar}</td>
            </tr>
            <tr>
                <td>Metode Bayar:</td>
                <td class="text-right">{$metode}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="footer">
            <p>Terima kasih telah berbelanja di Toko Firo!</p>
            <p>Barang yang sudah dibeli dapat ditukar maksimal 1x24 jam dengan struk resmi.</p>
        </div>

        <button type="button" class="btn-print" onclick="window.print()">Cetak Resi</button>
    </div>
</body>
</html>
HTML;
    }
}
