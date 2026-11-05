<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengembalianKasirController extends Controller
{
    /**
     * Halaman utama pengembalian barang.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $namaUser = $user->nama ?? $user->name ?? 'Kasir';
        $kasir = [
            'nama' => $namaUser,
            'inisial' => strtoupper(mb_substr($namaUser, 0, 1)),
            'role' => $user->role ?? 'KASIR',
        ];

        // Recent transactions for quick access
        $transaksiTerbaru = DB::select(
            'SELECT dp.kode_transaksi, dp.tanggal_transaksi, dp.total_bayar, dp.metode_bayar, dp.nama_pelanggan, p.nama as kasir_nama
             FROM daftar_penjualan dp
             LEFT JOIN pengguna p ON dp.id_kasir = p.id_pengguna
             ORDER BY dp.tanggal_transaksi DESC, dp.kode_transaksi DESC
             LIMIT 20'
        );

        // Recent returns (retur log from log_aktivitas)
        $riwayatRetur = DB::select(
            "SELECT la.waktu, la.keterangan, la.tipe_aksi, p.nama as pelaku
             FROM log_aktivitas la
             LEFT JOIN pengguna p ON la.id_pengguna = p.id_pengguna
             WHERE la.tipe_aksi = 'RETUR'
             ORDER BY la.waktu DESC
             LIMIT 10"
        );

        return view('kasir.pengembalian.index', [
            'kasir' => $kasir,
            'transaksiTerbaru' => $transaksiTerbaru,
            'riwayatRetur' => $riwayatRetur,
        ]);
    }

    /**
     * AJAX: Cari transaksi berdasarkan kode.
     */
    public function cariTransaksi(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['data' => [], 'message' => 'Minimal 2 karakter.']);
        }

        $results = DB::select(
            "SELECT dp.kode_transaksi, dp.tanggal_transaksi, dp.total_bayar, dp.metode_bayar, dp.nama_pelanggan, p.nama as kasir_nama
             FROM daftar_penjualan dp
             LEFT JOIN pengguna p ON dp.id_kasir = p.id_pengguna
             WHERE dp.kode_transaksi LIKE ?
             ORDER BY dp.tanggal_transaksi DESC
             LIMIT 10",
            ['%' . $q . '%']
        );

        return response()->json(['data' => $results]);
    }

    /**
     * AJAX: Detail transaksi beserta item yang bisa diretur.
     */
    public function detailTransaksi(string $nota): JsonResponse
    {
        // Get transaction header
        $transaksi = DB::select(
            'SELECT dp.kode_transaksi, dp.tanggal_transaksi, dp.total_bayar, dp.metode_bayar, dp.nama_pelanggan, p.nama as kasir_nama
             FROM daftar_penjualan dp
             LEFT JOIN pengguna p ON dp.id_kasir = p.id_pengguna
             WHERE dp.kode_transaksi = ?',
            [$nota]
        );

        if (empty($transaksi)) {
            return response()->json(['error' => 'Transaksi tidak ditemukan.'], 404);
        }

        // Get items with returnable qty
        // item_keluar = qty sold, sum retur_penjualan = qty already returned
        $items = DB::select(
            "SELECT dt.kode_item, di.nama_item, di.satuan, di.harga_jual,
                    SUM(dt.item_keluar) as total_terjual,
                    SUM(dt.retur_penjualan) as total_diretur
             FROM daftar_transaksi dt
             JOIN daftar_item di ON dt.kode_item = di.kode_item
             WHERE dt.kode_penjualan = ?
             GROUP BY dt.kode_item, di.nama_item, di.satuan, di.harga_jual
             HAVING (SUM(dt.item_keluar) - SUM(dt.retur_penjualan)) > 0",
            [$nota]
        );

        // Calculate returnable qty for each item
        $itemsFormatted = [];
        foreach ($items as $item) {
            $bisaDiretur = (int) $item->total_terjual - (int) $item->total_diretur;
            $itemsFormatted[] = [
                'kode_item' => $item->kode_item,
                'nama_item' => $item->nama_item,
                'satuan' => $item->satuan,
                'harga_jual' => (float) $item->harga_jual,
                'total_terjual' => (int) $item->total_terjual,
                'total_diretur' => (int) $item->total_diretur,
                'bisa_diretur' => $bisaDiretur,
            ];
        }

        return response()->json([
            'transaksi' => $transaksi[0],
            'items' => $itemsFormatted,
        ]);
    }

    /**
     * Proses pengembalian barang.
     */
    public function proses(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nota' => ['required', 'string', 'max:20'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.kode_item' => ['required', 'string', 'max:20'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'alasan' => ['nullable', 'string', 'max:500'],
        ]);

        $nota = $validated['nota'];
        $items = $validated['items'];
        $alasan = trim($validated['alasan'] ?? 'Pengembalian barang');
        $userId = Auth::id();

        // Verify transaction exists
        $transaksi = DB::select(
            'SELECT kode_transaksi FROM daftar_penjualan WHERE kode_transaksi = ?',
            [$nota]
        );
        if (empty($transaksi)) {
            return response()->json(['error' => 'Transaksi tidak ditemukan.'], 404);
        }

        $totalRefund = 0;
        $returnedItems = [];

        try {
            DB::beginTransaction();

            foreach ($items as $item) {
                $kodeItem = $item['kode_item'];
                $qty = (int) $item['qty'];

                // Call the SP — it validates qty and inserts retur row
                DB::statement('CALL sp_retur_penjualan(?, ?, ?)', [$nota, $kodeItem, $qty]);

                // Restore stock (SP doesn't do this)
                DB::statement(
                    'UPDATE daftar_item SET stok = stok + ? WHERE kode_item = ?',
                    [$qty, $kodeItem]
                );

                // Get item price for refund calculation
                $itemData = DB::select(
                    'SELECT harga_jual, nama_item FROM daftar_item WHERE kode_item = ?',
                    [$kodeItem]
                );
                $hargaJual = !empty($itemData) ? (float) $itemData[0]->harga_jual : 0;
                $subtotalRefund = $hargaJual * $qty;
                $totalRefund += $subtotalRefund;

                $returnedItems[] = [
                    'kode_item' => $kodeItem,
                    'nama_item' => !empty($itemData) ? $itemData[0]->nama_item : $kodeItem,
                    'qty' => $qty,
                    'subtotal' => $subtotalRefund,
                ];
            }

            // Log to log_aktivitas
            $itemSummary = implode(', ', array_map(
                fn($i) => $i['nama_item'] . ' x' . $i['qty'],
                $returnedItems
            ));

            DB::insert(
                "INSERT INTO log_aktivitas (waktu, keterangan, tipe_aksi, id_pengguna, data_sebelum, data_sesudah)
                 VALUES (NOW(), ?, 'RETUR', ?, ?, ?)",
                [
                    "Retur penjualan nota {$nota}: {$itemSummary}. Alasan: {$alasan}",
                    $userId,
                    json_encode(['nota' => $nota, 'items_before' => $items]),
                    json_encode(['returned_items' => $returnedItems, 'total_refund' => $totalRefund]),
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian barang berhasil diproses.',
                'nota' => $nota,
                'items' => $returnedItems,
                'total_refund' => $totalRefund,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Parse MySQL SIGNAL error messages
            $msg = $e->getMessage();
            if (preg_match('/(?:1644|SQLSTATE\[45000\]:.*?:\s*\d*\s*)(.*?)(?:\(Connection:|$)/', $msg, $m)) {
                $msg = trim($m[1]);
            }

            return response()->json(['error' => $msg], 422);
        }
    }
}

