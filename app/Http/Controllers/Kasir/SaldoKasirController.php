<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramReportService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaldoKasirController extends Controller
{
    public function index(): View
    {
        $hariIni = Carbon::today();
        $tanggal = $hariIni->toDateString();
        $user = auth()->user();
        $kasirId = auth()->id() ?? $user?->id_pengguna;
        $isAdminOrPemilik = $user && in_array(strtoupper($user->role ?? ''), ['ADMIN', 'PEMILIK']);

        // 1. Ambil data saldo awal hari ini dari saldo_harian (read-only, tanpa mutasi updateOrInsert)
        $saldoHariIni = DB::table('saldo_harian')
            ->where('tanggal', $tanggal)
            ->first();

        if ($saldoHariIni && $saldoHariIni->saldo_awal !== null) {
            $saldoAwal = (float) $saldoHariIni->saldo_awal;
        } else {
            // Fallback ke saldo_akhir hari sebelumnya
            $saldoSebelumnya = DB::table('saldo_harian')
                ->where('tanggal', '<', $tanggal)
                ->orderByDesc('tanggal')
                ->value('saldo_akhir');
            $saldoAwal = (float) ($saldoSebelumnya ?? 0.0);
        }

        // 2. Ambil transaksi penjualan hari ini
        $penjualanQuery = DB::table('daftar_penjualan')
            ->whereDate('tanggal_transaksi', $tanggal);

        // Jika bukan admin/pemilik, batasi hanya transaksi kasir bersangkutan
        if (!$isAdminOrPemilik && $kasirId) {
            $penjualanQuery->where('id_kasir', $kasirId);
        }

        $penjualan = (clone $penjualanQuery)
            ->orderByDesc('kode_transaksi')
            ->get();

        // 3. Akumulasi transaksi TUNAI dan NON-TUNAI
        $totalTunai = (float) (clone $penjualanQuery)
            ->whereRaw('UPPER(COALESCE(metode_bayar, "TUNAI")) = ?', ['TUNAI'])
            ->sum('total_bayar');

        $totalNonTunai = (float) (clone $penjualanQuery)
            ->whereRaw('UPPER(COALESCE(metode_bayar, "TUNAI")) != ?', ['TUNAI'])
            ->sum('total_bayar');

        $totalSemua = (float) $penjualan->sum('total_bayar');
        $jumlahTransaksi = $penjualan->count();

        // 4. Hitung kas keluar / retur penjualan dari transaksi hari ini
        $kodeTransaksi = $penjualan->pluck('kode_transaksi');
        $kasKeluar = 0.0;
        if ($kodeTransaksi->isNotEmpty()) {
            $kasKeluar = (float) (DB::table('daftar_transaksi')
                ->join('daftar_item', 'daftar_transaksi.kode_item', '=', 'daftar_item.kode_item')
                ->whereIn('daftar_transaksi.kode_penjualan', $kodeTransaksi)
                ->select(DB::raw('SUM(COALESCE(daftar_transaksi.retur_penjualan, 0) * COALESCE(daftar_item.harga_jual, 0)) as total_retur'))
                ->value('total_retur') ?? 0.0);
        }

        // 5. Hitung saldo akhir kas fisik kasir secara dinamis (Saldo Awal + Penjualan Tunai - Kas Keluar)
        $saldoAkhir = $saldoAwal + $totalTunai - $kasKeluar;

        $ringkasan = [
            'tanggal'               => $hariIni->translatedFormat('l, d M Y'),
            'nominal_saldo'         => 'Rp ' . number_format($saldoAkhir, 0, ',', '.'),
            'saldo_awal'            => 'Rp ' . number_format($saldoAwal, 0, ',', '.'),
            'modal_awal'            => 'Rp ' . number_format($saldoAwal, 0, ',', '.'),
            'total_penjualan_tunai' => 'Rp ' . number_format($totalTunai, 0, ',', '.'),
            'total_non_tunai'       => 'Rp ' . number_format($totalNonTunai, 0, ',', '.'),
            'kas_keluar'            => 'Rp ' . number_format($kasKeluar, 0, ',', '.'),
            'saldo_akhir'           => 'Rp ' . number_format($saldoAkhir, 0, ',', '.'),
            'total_transaksi'       => $jumlahTransaksi,
            'rata_rata'             => 'Rp ' . number_format(
                $jumlahTransaksi > 0 ? $totalSemua / $jumlahTransaksi : 0, 0, ',', '.'
            ),
        ];

        // 6. Ambil detail barang untuk setiap transaksi
        $detail = DB::table('daftar_transaksi')
            ->whereIn('kode_penjualan', $kodeTransaksi)
            ->select(
                'kode_penjualan',
                DB::raw('GROUP_CONCAT(DISTINCT kode_item SEPARATOR ", ") as item'),
                DB::raw('MAX(tanggal_log) as waktu')
            )
            ->groupBy('kode_penjualan')
            ->get()
            ->keyBy('kode_penjualan');

        // Susun riwayat transaksi dengan metode pembayaran yang akurat
        $riwayatTransaksi = $penjualan->map(function ($trx) use ($detail) {
            $d = $detail->get($trx->kode_transaksi);
            $metodeRaw = strtoupper((string) ($trx->metode_bayar ?? 'TUNAI'));
            $metodeLabel = ($metodeRaw === 'TUNAI') ? 'Tunai' : ($trx->metode_bayar ?: 'QRIS');

            return [
                'waktu' => $d && $d->waktu
                    ? Carbon::parse($d->waktu)->format('H:i')
                    : (isset($trx->tanggal_transaksi) ? Carbon::parse($trx->tanggal_transaksi)->format('H:i') : '--:--'),
                'no_transaksi' => $trx->kode_transaksi,
                'item' => $d?->item ?? 'Tidak ada detail barang',
                'total' => 'Rp ' . number_format((float) $trx->total_bayar, 0, ',', '.'),
                'metode' => $metodeLabel,
            ];
        });

        $nama = $user ? ($user->nama ?? $user->username ?? 'Kasir') : 'Kasir';
        $role = $user ? ($user->role ?? 'Kasir') : 'Kasir';
        $roleLabel = in_array(strtoupper($role), ['ADMIN', 'PEMILIK']) ? 'Admin' : 'Kasir';

        $kasir = [
            'nama' => $nama,
            'inisial' => strtoupper(substr((string) $nama, 0, 1)),
            'role' => $roleLabel,
        ];

        return view('kasir.saldo.index', compact(
            'ringkasan',
            'riwayatTransaksi',
            'kasir'
        ));
    }

    /**
     * Ekspor rekap saldo kasir hari ini sebagai dokumen PDF resmi.
     */
    public function exportPdf(Request $request): Response
    {
        $today = date('Y-m-d');
        /** @var TelegramReportService $reportService */
        $reportService = app(TelegramReportService::class);
        $pdfBinary = $reportService->generatePdfReport($today, $today);
        $filename = 'Rekap_Saldo_Kasir_' . $today . '.pdf';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Mengirim rekap saldo kasir hari ini ke bot Telegram Admin / Group.
     */
    public function kirimTelegram(Request $request): JsonResponse|RedirectResponse
    {
        $today = date('Y-m-d');
        /** @var TelegramReportService $reportService */
        $reportService = app(TelegramReportService::class);
        /** @var TelegramBotService $botService */
        $botService = app(TelegramBotService::class);

        $chatId = config('services.telegram.admin_chat_id') ?: config('services.telegram.chat_id');
        if (empty($chatId)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Chat ID Telegram belum dikonfigurasi.'], 400);
            }
            return redirect()->back()->with('error', 'Chat ID Telegram belum dikonfigurasi.');
        }

        $pdfBinary = $reportService->generatePdfReport($today, $today);
        $summary = $reportService->getSummaryData();
        $caption = $reportService->buildSalesCaption($summary);
        $filename = 'Rekap_Saldo_Kasir_' . $today . '.pdf';

        $res = $botService->sendDocument($chatId, $pdfBinary, $filename, $caption);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Rekap saldo kasir berhasil dikirim ke Telegram!']);
        }

        return redirect()->back()->with('success', 'Rekap saldo kasir berhasil dikirim ke Telegram!');
    }
}