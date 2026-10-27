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
        // Proteksi autentikasi untuk rute kasir.transaksi.index jika diterapkan
        if ($request->routeIs('kasir.transaksi.index') && !auth()->check()) {
            return redirect()->guest(route('login'));
        }

        // Ambil katalog barang aktif dengan stok > 0
        $items = ItemBarang::query()
            ->where('stok', '>', 0)
            ->orderBy('nama_item', 'asc')
            ->limit(50)
            ->get();

        if ($items->isEmpty()) {
            $items = ItemBarang::query()->orderBy('nama_item', 'asc')->limit(50)->get();
        }

        $katalog = $items->map(function ($it) {
            return [
                'kode' => $it->kode_item,
                'nama' => $it->nama_item,
                'harga' => (float) $it->harga_jual,
                'stok' => (int) $it->stok,
                'satuan' => $it->satuan ?: 'pcs',
            ];
        })->toArray();

        // Ambil keranjang dari session jika ada
        $keranjangMap = session('keranjang', []);
        $keranjang = array_values($keranjangMap);
        $totalBelanja = (float) array_sum(array_column($keranjang, 'subtotal'));

        $user = auth()->user();
        $nama = $user ? ($user->nama ?? $user->name ?? 'Kasir') : 'Rina Putri';
        $role = $user ? ($user->role ?? 'Kasir') : 'Kasir';
        $roleLabel = in_array(strtoupper($role), ['ADMIN', 'PEMILIK']) ? 'Admin' : 'Kasir';

        $kasir = [
            'nama' => $nama,
            'inisial' => strtoupper(substr((string) $nama, 0, 1)),
            'role' => $roleLabel,
        ];

        return view('kasir.transaksi.index', compact('katalog', 'keranjang', 'totalBelanja', 'kasir'));
    }

    /**
     * Memperbarui keranjang belanja kasir dalam session (tambah, kurangi, set, kosongkan).
     */
    public function updateKeranjang(Request $request): JsonResponse
    {
        $action = (string) $request->input('action', 'tambah');
        $kode = trim((string) $request->input('kode_item', $request->input('kode', '')));
        $keranjang = session('keranjang', []);

        if ($action === 'kosongkan') {
            session()->forget('keranjang');
            return response()->json([
                'status' => 'success',
                'message' => 'Keranjang berhasil dikosongkan.',
                'keranjang' => [],
                'total_belanja' => 0,
                'total_item' => 0,
            ]);
        }

        if (empty($kode)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode item tidak boleh kosong.',
            ], 422);
        }

        $barang = ItemBarang::where('kode_item', $kode)->first();
        if (!$barang) {
            return response()->json([
                'status' => 'error',
                'message' => "Barang dengan kode '{$kode}' tidak ditemukan.",
            ], 404);
        }

        $stokTersedia = (int) $barang->stok;
        $qtySekarang = isset($keranjang[$kode]) ? (int) $keranjang[$kode]['qty'] : 0;

        if ($action === 'tambah') {
            $tambahQty = max(1, (int) $request->input('qty', 1));
            $targetQty = $qtySekarang + $tambahQty;

            if ($targetQty > $stokTersedia) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Stok '{$barang->nama_item}' tidak mencukupi (sisa: {$stokTersedia}, diminta: {$targetQty}).",
                ], 422);
            }

            $keranjang[$kode] = [
                'kode' => $barang->kode_item,
                'nama' => $barang->nama_item,
                'harga' => (float) $barang->harga_jual,
                'satuan' => $barang->satuan ?: 'pcs',
                'qty' => $targetQty,
                'stok' => $stokTersedia,
                'subtotal' => (float) $barang->harga_jual * $targetQty,
            ];
        } elseif ($action === 'kurang') {
            $kurangQty = max(1, (int) $request->input('qty', 1));
            $targetQty = $qtySekarang - $kurangQty;

            if ($targetQty <= 0) {
                unset($keranjang[$kode]);
            } else {
                $keranjang[$kode]['qty'] = $targetQty;
                $keranjang[$kode]['subtotal'] = (float) $keranjang[$kode]['harga'] * $targetQty;
            }
        } elseif ($action === 'hapus') {
            unset($keranjang[$kode]);
        } elseif ($action === 'set') {
            $targetQty = (int) $request->input('qty', 1);
            if ($targetQty <= 0) {
                unset($keranjang[$kode]);
            } else {
                if ($targetQty > $stokTersedia) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stok '{$barang->nama_item}' tidak mencukupi (sisa: {$stokTersedia}).",
                    ], 422);
                }
                $keranjang[$kode] = [
                    'kode' => $barang->kode_item,
                    'nama' => $barang->nama_item,
                    'harga' => (float) $barang->harga_jual,
                    'satuan' => $barang->satuan ?: 'pcs',
                    'qty' => $targetQty,
                    'stok' => $stokTersedia,
                    'subtotal' => (float) $barang->harga_jual * $targetQty,
                ];
            }
        }

        session(['keranjang' => $keranjang]);

        $totalBelanja = 0;
        $totalItem = 0;
        foreach ($keranjang as $item) {
            $totalBelanja += $item['subtotal'];
            $totalItem += $item['qty'];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Keranjang berhasil diperbarui.',
            'keranjang' => array_values($keranjang),
            'total_belanja' => $totalBelanja,
            'total_item' => $totalItem,
        ]);
    }

    /**
     * Memproses penyimpanan transaksi penjualan POS kasir.
     */
    public function simpan(SimpanTransaksiRequest $request): JsonResponse
    {
        try {
            $kasirId = (int) (auth()->id() ?? auth()->user()?->id_pengguna ?? 1);
            $transaksi = $this->transaksiKasirService->prosesTransaksi($request->validated(), $kasirId);

            // Bersihkan session keranjang setelah transaksi berhasil
            session()->forget('keranjang');

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

        $pengaturan = app(\App\Services\PengaturanTokoService::class)->getSettings();

        if (view()->exists('kasir.transaksi.struk')) {
            return view('kasir.transaksi.struk', compact('transaksi', 'pengaturan'));
        }

        if (view()->exists('kasir.transaksi.cetak')) {
            return view('kasir.transaksi.cetak', compact('transaksi', 'pengaturan'));
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
        $tokoService = app(\App\Services\PengaturanTokoService::class);
        $settings = $tokoService->getSettings();

        $namaToko = htmlspecialchars((string) ($settings['nama_toko'] ?? 'TOKO FIRO'));
        $headerStruk = htmlspecialchars((string) ($settings['header_struk'] ?? 'Alat Tulis Kantor & Jasa Fotocopy'));
        $alamatToko = htmlspecialchars((string) ($settings['alamat'] ?? ''));
        $teleponToko = htmlspecialchars((string) ($settings['telepon'] ?? '081234567890'));
        $footerStruk = htmlspecialchars((string) ($settings['footer_struk'] ?? 'Terima kasih telah berbelanja!'));

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
            <h1>{$namaToko}</h1>
            <p>{$headerStruk}</p>
            <p>{$alamatToko}</p>
            <p>Telepon: {$teleponToko}</p>
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
            <p>{$footerStruk}</p>
            <p>Barang yang sudah dibeli dapat ditukar maksimal 1x24 jam dengan struk resmi.</p>
        </div>

        <button type="button" class="btn-print" onclick="window.print()">Cetak Resi</button>
    </div>
</body>
</html>
HTML;
    }
}
