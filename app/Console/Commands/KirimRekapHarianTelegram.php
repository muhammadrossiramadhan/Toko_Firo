<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramReportService;
use Illuminate\Console\Command;

class KirimRekapHarianTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:kirim-rekap-telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim rekap laporan penjualan harian beserta dokumen PDF ke Telegram grup toko';

    /**
     * Execute the console command.
     */
    public function handle(
        TelegramBotService $botService,
        TelegramReportService $reportService
    ): int {
        $chatId = config('services.telegram.chat_id');

        if (empty($chatId)) {
            $this->error('TELEGRAM_GROUP_CHAT_ID belum dikonfigurasi di services.telegram.');
            return self::FAILURE;
        }

        $this->info('Mengambil data ringkasan penjualan hari ini...');
        $summary = $reportService->getSummaryData();

        $this->info('Menghasilkan berkas PDF Laporan Penjualan...');
        $pdfBinary = $reportService->generateSalesPdfBinary($summary);
        $caption = $reportService->buildSalesCaption($summary);
        $filename = 'Laporan_Penjualan_Toko_Firo_' . date('Ymd_His') . '.pdf';

        $this->info("Mengirim dokumen PDF ke chat ID: {$chatId}...");
        $response = $botService->sendDocument($chatId, $pdfBinary, $filename, $caption);

        if ($response && ($response['ok'] ?? false) === true) {
            $this->info('Berhasil mengirimkan rekap harian ke Telegram!');
            return self::SUCCESS;
        }

        $errorMsg = $response['description'] ?? 'Gagal menghubungi Telegram API.';
        $this->error("Gagal mengirimkan rekap harian: {$errorMsg}");

        return self::FAILURE;
    }
}

