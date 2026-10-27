<?php

namespace App\Console\Commands;

use App\Services\LaporanPenjualanService;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KirimRekapHarianTelegram extends Command
{
    /**
     * Nama dan signature dari perintah console.
     *
     * @var string
     */
    protected $signature = 'telegram:kirim-rekap-harian';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Kirim rekap laporan penjualan harian otomatis ke Telegram pada pukul 21:00 WIB';

    /**
     * Eksekusi perintah console.
     */
    public function handle(
        LaporanPenjualanService $laporanService,
        TelegramReportService $reportService,
        TelegramBotService $botService
    ): int {
        try {
            // a. Cek status toggle notifikasi di database
            if ($this->isRekapDinonaktifkan()) {
                $this->info('Pengiriman rekap laporan penjualan harian dinonaktifkan pada pengaturan notifikasi.');
                return self::SUCCESS;
            }

            // b. Ambil rentang tanggal hari ini
            $hariIni = now()->toDateString();
            $this->info("Menyiapkan rekap penjualan harian untuk tanggal: {$hariIni}...");

            // c. Hitung ringkasan penjualan hari ini via LaporanPenjualanService
            $ringkasan = $laporanService->ringkasan($hariIni, $hariIni);
            $transaksiList = $laporanService->transaksi($hariIni, $hariIni, 50);

            // d. Generate file PDF via TelegramReportService
            $pdfBinary = $reportService->generatePdfReport($hariIni, $hariIni);
            $filename = 'Rekap_Penjualan_' . $hariIni . '.pdf';

            // e. Siapkan caption ringkas: Total Omset, Jumlah Nota, Barang Terlaris, dan Kasir yang bertugas
            $kasirList = collect($transaksiList->items())
                ->pluck('nama_kasir')
                ->filter(fn ($nama) => $nama && $nama !== '-')
                ->unique()
                ->values()
                ->all();

            $kasirBertugas = !empty($kasirList) ? implode(', ', $kasirList) : 'Tidak ada transaksi kasir';
            $totalOmset = number_format((float) ($ringkasan['total_penjualan'] ?? 0), 0, ',', '.');
            $jumlahNota = (int) ($ringkasan['jumlah_nota'] ?? 0);
            $barangTerlaris = $ringkasan['barang_terlaris'] ?: '-';
            $tanggalIndo = Carbon::parse($hariIni)->locale('id')->isoFormat('dddd, D MMMM Y');

            $caption = "📊 <b>REKAP LAPORAN PENJUALAN HARIAN TOKO FIRO</b>\n" .
                       "📅 <b>Tanggal:</b> {$tanggalIndo}\n\n" .
                       "💰 <b>Total Omset:</b> Rp {$totalOmset}\n" .
                       "🧾 <b>Jumlah Nota:</b> {$jumlahNota} transaksi\n" .
                       "⭐ <b>Barang Terlaris:</b> {$barangTerlaris}\n" .
                       "👤 <b>Kasir Bertugas:</b> {$kasirBertugas}\n\n" .
                       "<i>Dokumen rekap resmi otomatis pukul 21:00 WIB terlampir di atas.</i>";

            // f. Kirim dokumen PDF dan caption ke Telegram Admin Chat ID dan/atau Group Chat ID
            $chatIds = array_filter(array_unique([
                config('services.telegram.admin_chat_id'),
                config('services.telegram.chat_id'),
                $this->getChatIdPemilikFromDb(),
                env('TELEGRAM_ADMIN_CHAT_ID'),
                env('TELEGRAM_GROUP_CHAT_ID'),
            ]));

            if (empty($chatIds)) {
                $this->warn('Kredensial Telegram Chat ID belum terkonfigurasi di server.');
                Log::warning('KirimRekapHarianTelegram: Chat ID Telegram belum terkonfigurasi.');
                return self::FAILURE;
            }

            foreach ($chatIds as $targetChatId) {
                $res = $botService->sendDocument($targetChatId, $pdfBinary, $filename, $caption);

                // Fallback pesan teks jika upload dokumen PDF terkendala
                if ($res === false || (is_array($res) && isset($res['ok']) && !$res['ok'])) {
                    $botService->sendMessage($targetChatId, $caption);
                }
            }

            $this->info('Rekap laporan penjualan harian berhasil dikirim ke Telegram.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            // g. Tangani error dengan try-catch dan logging agar scheduler tidak crash
            Log::error('KirimRekapHarianTelegram gagal: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error('Gagal mengirim rekap laporan harian: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Memeriksa apakah toggle rekap harian dinonaktifkan di tabel pengaturan_notifikasi.
     */
    protected function isRekapDinonaktifkan(): bool
    {
        try {
            $status = DB::table('pengaturan_notifikasi')
                ->where('kunci', 'laporan_otomatis')
                ->value('nilai');

            if ($status !== null) {
                $str = strtolower(trim((string) $status));
                return in_array($str, ['0', 'false', 'off', 'no', 'nonaktif'], true);
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal membaca tabel pengaturan_notifikasi: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Mengambil chat ID pemilik dari tabel pengaturan_notifikasi jika tersedia.
     */
    protected function getChatIdPemilikFromDb(): ?string
    {
        try {
            $chatId = DB::table('pengaturan_notifikasi')
                ->where('kunci', 'telegram_chat_id_pemilik')
                ->value('nilai');

            return !empty($chatId) ? (string) $chatId : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}

