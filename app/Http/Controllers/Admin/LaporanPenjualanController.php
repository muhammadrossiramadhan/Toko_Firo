<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LaporanPenjualanService;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramReportService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LaporanPenjualanController extends Controller
{
    /**
     * Jumlah baris riwayat transaksi per halaman paginasi.
     */
    public const PER_HALAMAN = 5;

    public function __construct(
        protected LaporanPenjualanService $laporanService
    ) {}

    /**
     * Menampilkan halaman Laporan Penjualan (Figma P11).
     */
    public function index(Request $request): View
    {
        $pesan = null;
        $rentangInput = (string) $request->query('rentang', 'hari-ini');
        $hariIni = date('Y-m-d');

        // Daftar putih opsi rentang
        $daftarPutihRentang = ['hari-ini', 'hari_ini', '7-hari', 'tujuh_hari', '7_hari', 'bulan-ini', 'bulan_ini', 'kustom'];
        $rentang = in_array($rentangInput, $daftarPutihRentang, true) ? $rentangInput : 'hari-ini';

        if ($rentang === '7-hari' || $rentang === 'tujuh_hari' || $rentang === '7_hari') {
            $rentang = '7-hari';
            $mulai = date('Y-m-d', strtotime('-6 days'));
            $sampai = $hariIni;
        } elseif ($rentang === 'bulan-ini' || $rentang === 'bulan_ini') {
            $rentang = 'bulan-ini';
            $mulai = date('Y-m-01');
            $sampai = $hariIni;
        } elseif ($rentang === 'kustom') {
            $reqMulai = (string) $request->query('mulai');
            $reqSampai = (string) $request->query('sampai');

            $formatValid = $this->validasiTanggal($reqMulai) && $this->validasiTanggal($reqSampai);

            if (! $formatValid || $reqSampai < $reqMulai) {
                $mulai = $hariIni;
                $sampai = $hariIni;
                $pesan = 'Tanggal tidak valid, ditampilkan hari ini.';
            } else {
                $selisihHari = (strtotime($reqSampai) - strtotime($reqMulai)) / 86400;
                if ($selisihHari > 366) {
                    $mulai = $hariIni;
                    $sampai = $hariIni;
                    $pesan = 'Rentang terlalu panjang, maksimal 1 tahun.';
                } else {
                    $mulai = $reqMulai;
                    $sampai = $reqSampai;
                }
            }
        } else {
            $rentang = 'hari-ini';
            $mulai = $hariIni;
            $sampai = $hariIni;
        }

        $filter = [
            'rentang' => $rentang,
            'mulai' => $mulai,
            'sampai' => $sampai,
        ];

        $ringkasan = $this->laporanService->ringkasan($mulai, $sampai);
        $transaksi = $this->laporanService->transaksi($mulai, $sampai, self::PER_HALAMAN);

        return view('admin.laporan.penjualan', [
            'filter' => $filter,
            'ringkasan' => $ringkasan,
            'transaksi' => $transaksi,
            'pesan' => $pesan,
        ]);
    }

    /**
     * Mengirimkan berkas laporan rekap PDF atau ringkasan penjualan ke Telegram Admin.
     */
    public function kirimTelegram(Request $request): JsonResponse|RedirectResponse
    {
        $hariIni = date('Y-m-d');
        $mulai = (string) ($request->input('mulai') ?: $hariIni);
        $sampai = (string) ($request->input('sampai') ?: $hariIni);

        if ($request->filled('rentang') && !$request->filled('mulai')) {
            $rentang = (string) $request->input('rentang');
            if ($rentang === '7-hari' || $rentang === 'tujuh_hari' || $rentang === '7_hari') {
                $mulai = date('Y-m-d', strtotime('-6 days'));
                $sampai = $hariIni;
            } elseif ($rentang === 'bulan-ini' || $rentang === 'bulan_ini') {
                $mulai = date('Y-m-01');
                $sampai = $hariIni;
            }
        }

        if (! $this->validasiTanggal($mulai) || ! $this->validasiTanggal($sampai) || $sampai < $mulai) {
            $mulai = $hariIni;
            $sampai = $hariIni;
        }

        $chatId = config('services.telegram.admin_chat_id')
            ?: config('services.telegram.chat_id')
            ?: (app()->environment('testing') ? null : (env('TELEGRAM_ADMIN_CHAT_ID') ?: env('TELEGRAM_GROUP_CHAT_ID')));

        $token = config('services.telegram.bot_token')
            ?: (app()->environment('testing') ? null : env('TELEGRAM_BOT_TOKEN'));

        if (empty($token) || empty($chatId)) {
            Log::warning('Telegram token atau admin chat ID belum terkonfigurasi.');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Konfigurasi Telegram (Token atau Chat ID) belum diatur di server.',
                ], 500);
            }

            return back()->with('pesan', 'Konfigurasi Telegram (Token atau Chat ID) belum diatur di server.');
        }

        try {
            $ringkasan = $this->laporanService->ringkasan($mulai, $sampai);
            $transaksiList = $this->laporanService->transaksi($mulai, $sampai, 50);

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
                : Carbon::parse($mulai)->format('d/m/Y') . ' s/d ' . Carbon::parse($sampai)->format('d/m/Y');

            $reportData = [
                'tanggal'          => $tanggalLabel,
                'total_penjualan'  => (float) $ringkasan['total_penjualan'],
                'jumlah_transaksi' => (int) $ringkasan['jumlah_nota'],
                'barang_terjual'   => (int) $ringkasan['barang_terjual'],
                'produk_terlaris'  => $ringkasan['barang_terlaris'] ?: '-',
                'saldo_kasir'      => (float) $ringkasan['total_penjualan'],
                'modal_awal'       => 0,
                'transaksi'        => $transaksiItems,
            ];

            /** @var TelegramReportService $reportService */
            $reportService = app(TelegramReportService::class);
            /** @var TelegramBotService $botService */
            $botService = app(TelegramBotService::class);

            $pdfBinary = $reportService->generateSalesPdfBinary($reportData);
            $caption = $reportService->buildSalesCaption($reportData);
            $filename = 'Laporan_Penjualan_' . $mulai . '_sd_' . $sampai . '.pdf';

            $responseDoc = $botService->sendDocument($chatId, $pdfBinary, $filename, $caption);

            if ($responseDoc === false || (is_array($responseDoc) && isset($responseDoc['ok']) && !$responseDoc['ok'])) {
                $botService->sendMessage($chatId, $caption);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Laporan penjualan berhasil dikirim ke Telegram!',
                ], 200);
            }

            return back()->with('pesan', 'Laporan penjualan berhasil dikirim ke Telegram!');
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim laporan penjualan ke Telegram: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengirim laporan ke Telegram: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('pesan', 'Gagal mengirim laporan ke Telegram: ' . $e->getMessage());
        }
    }

    /**
     * Memeriksa apakah format tanggal valid (Y-m-d).
     */
    private function validasiTanggal(?string $tanggal): bool
    {
        if (! $tanggal) {
            return false;
        }

        $d = DateTime::createFromFormat('Y-m-d', $tanggal);

        return $d && $d->format('Y-m-d') === $tanggal;
    }
}
