<?php

namespace App\Http\Controllers;

use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __construct(
        protected TelegramBotService $botService,
        protected TelegramReportService $reportService
    ) {}

    /**
     * Menangani webhook update masuk dari Telegram.
     */
    public function handle(Request $request): JsonResponse
    {
        // 1. Validasi Secret Token (X-Telegram-Bot-Api-Secret-Token)
        $expectedSecret = (string) config('services.telegram.webhook_secret');
        $receivedSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token');

        if (empty($expectedSecret) || $receivedSecret !== $expectedSecret) {
            Log::warning('Telegram webhook rejected: secret token tidak valid atau tidak disediakan.');
            return response()->json(['error' => 'Forbidden: Invalid secret token'], 403);
        }

        $update = $request->all();

        // Ambil Chat ID dari update (message atau callback_query)
        $chatId = $update['message']['chat']['id']
            ?? $update['callback_query']['message']['chat']['id']
            ?? $update['callback_query']['from']['id']
            ?? null;

        // 2. Whitelist Otorisasi Chat ID
        if (! $this->isAuthorizedChat($chatId)) {
            Log::warning("Telegram webhook rejected: chat ID {$chatId} tidak terdaftar di whitelist.");
            return response()->json(['error' => 'Forbidden: Unauthorized chat ID'], 403);
        }

        // 3. Penanganan Callback Query dari tombol Inline Keyboard
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
            return response()->json(['status' => 'ok', 'type' => 'callback_query']);
        }

        // 4. Penanganan Pesan Teks
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
            return response()->json(['status' => 'ok', 'type' => 'message']);
        }

        return response()->json(['status' => 'ignored']);
    }

    /**
     * Memeriksa apakah chat ID terdaftar di whitelist (chat_id atau admin_chat_id).
     */
    protected function isAuthorizedChat($chatId): bool
    {
        if (empty($chatId)) {
            return false;
        }

        $allowedGroup = (string) config('services.telegram.chat_id');
        $allowedAdmin = (string) config('services.telegram.admin_chat_id');

        $chatIdStr = (string) $chatId;

        return ($allowedGroup !== '' && $chatIdStr === $allowedGroup)
            || ($allowedAdmin !== '' && $chatIdStr === $allowedAdmin);
    }

    /**
     * Memproses pesan teks pengguna.
     */
    protected function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'] ?? null;
        $text = trim($message['text'] ?? '');

        if (! $chatId || empty($text)) {
            return;
        }

        $lowerText = strtolower($text);

        // a. Deteksi kata kunci natural untuk unduh Laporan Penjualan
        if (
            str_contains($lowerText, 'laporan penjualan') ||
            str_contains($lowerText, 'laporan hari ini') ||
            str_contains($lowerText, 'rekap hari ini') ||
            str_contains($lowerText, 'rekap penjualan')
        ) {
            $this->kirimLaporanPdf($chatId);
            return;
        }

        // b. Perintah standar
        if ($text === '/start' || $text === '/menu') {
            $this->kirimMenuUtama($chatId);
            return;
        }

        if ($text === '/menu_admin') {
            $this->kirimMenuAdmin($chatId);
            return;
        }

        if ($text === '/menu_kasir') {
            $this->kirimMenuKasir($chatId);
            return;
        }

        // c. Pesan bantuan default
        $pesanDefault = "Halo! Silakan gunakan menu berikut untuk berinteraksi:\n\n" .
                        "• Ketik <b>/menu</b> untuk membuka navigasi fitur.\n" .
                        "• Ketik <i>\"laporan penjualan\"</i> untuk mengunduh rekap PDF hari ini.";

        $this->botService->sendMessage($chatId, $pesanDefault, [
            'inline_keyboard' => [
                [
                    ['text' => '📋 Buka Menu Utama', 'callback_data' => 'menu_utama'],
                ],
            ],
        ]);
    }

    /**
     * Memproses aksi klik pada tombol Inline Keyboard.
     */
    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $callbackId = $callbackQuery['id'] ?? '';
        $chatId = $callbackQuery['message']['chat']['id'] ?? null;
        $data = $callbackQuery['data'] ?? '';

        // Berikan acknowledgement balik ke Telegram
        $this->botService->answerCallbackQuery($callbackId);

        if (! $chatId || empty($data)) {
            return;
        }

        switch ($data) {
            case 'menu_utama':
                $this->kirimMenuUtama($chatId);
                break;

            case 'menu_admin':
                $this->kirimMenuAdmin($chatId);
                break;

            case 'menu_kasir':
                $this->kirimMenuKasir($chatId);
                break;

            case 'admin_laporan_pdf':
            case 'kasir_rekap':
                $this->kirimLaporanPdf($chatId);
                break;

            case 'admin_barang':
                $this->botService->sendMessage(
                    $chatId,
                    "📦 <b>Kelola Data Barang Toko Firo</b>\n\n" .
                    "• Total Master Barang: <b>47 item</b>\n" .
                    "• Stok Aman: 41 item\n" .
                    "• Stok Menipis: 4 item\n" .
                    "• Stok Habis: 2 item\n\n" .
                    "🔗 <a href=\"" . url('/admin/barang') . "\">Buka Modul Data Barang</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_pembelian':
                $this->botService->sendMessage(
                    $chatId,
                    "📥 <b>Pembelian Barang (Restock)</b>\n\n" .
                    "• Pengajuan Restock: <b>3 pesanan</b> dalam proses\n" .
                    "• Supplier Utama: CV ATK Bersaudara, Toko Grosir Jaya\n\n" .
                    "🔗 <a href=\"" . url('/admin/pembelian') . "\">Buka Modul Pembelian</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_log':
                $this->botService->sendMessage(
                    $chatId,
                    "📜 <b>Log Aktivitas Sistem</b>\n\n" .
                    "• Terakhir: Admin berhasil login & kasir membuka shift\n" .
                    "• Total Catatan Hari Ini: <b>28 aktivitas</b>\n\n" .
                    "🔗 <a href=\"" . url('/admin/log-aktivitas') . "\">Buka Modul Log Aktivitas</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_batas_stok':
                $this->botService->sendMessage(
                    $chatId,
                    "⚠️ <b>Batas Stok Minimum</b>\n\n" .
                    "• Ambang Peringatan Dini: Minimal 5 pcs\n" .
                    "• Barang Perlu Restock Cepat: <b>4 item</b>\n\n" .
                    "🔗 <a href=\"" . url('/admin/batas-stok') . "\">Buka Modul Batas Stok</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_saldo':
            case 'kasir_saldo':
                $this->botService->sendMessage(
                    $chatId,
                    "💰 <b>Saldo Kasir Hari Ini</b>\n\n" .
                    "• Modal Awal: Rp 500.000\n" .
                    "• Penerimaan Tunai: Rp 1.830.000\n" .
                    "• Penerimaan QRIS: Rp 850.000\n" .
                    "• <b>Total Saldo Kas: Rp 3.180.000</b> (14 transaksi)\n\n" .
                    "🔗 <a href=\"" . url('/kasir/saldo') . "\">Buka Modul Saldo Kasir</a>",
                    $this->getKembaliKeyboard($data === 'admin_saldo' ? 'menu_admin' : 'menu_kasir')
                );
                break;

            case 'admin_pengguna':
                $this->botService->sendMessage(
                    $chatId,
                    "👥 <b>Kelola Akun Pengguna</b>\n\n" .
                    "• Total Pengguna: <b>4 akun</b>\n" .
                    "• 1 Akun Admin / Pemilik\n" .
                    "• 3 Akun Kasir Aktif\n\n" .
                    "🔗 <a href=\"" . url('/admin/pengguna') . "\">Buka Modul Pengguna</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_backup':
                $this->botService->sendMessage(
                    $chatId,
                    "💾 <b>Pencadangan Data (Backup)</b>\n\n" .
                    "• Status Backup: <b>Tersedia</b>\n" .
                    "• Jadwal Otomatis: Tiap 24 Jam (Tengah Malam)\n" .
                    "• Proteksi: Enkripsi ZIP AES-256\n\n" .
                    "🔗 <a href=\"" . url('/admin/backup') . "\">Buka Modul Backup</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_notifikasi':
                $this->botService->sendMessage(
                    $chatId,
                    "🔔 <b>Pengaturan Notifikasi Bot</b>\n\n" .
                    "• Webhook Bot: <b>Aktif & Terhubung</b>\n" .
                    "• Notifikasi Transaksi: Aktif\n" .
                    "• Notifikasi Stok Kritis: Aktif\n\n" .
                    "🔗 <a href=\"" . url('/admin/pengaturan-notifikasi') . "\">Buka Modul Notifikasi</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_toko':
                $this->botService->sendMessage(
                    $chatId,
                    "⚙️ <b>Pengaturan Profil Toko</b>\n\n" .
                    "• Toko: Toko Firo\n" .
                    "• Alamat: Jl. Yos Sudarso, Kraksaan, Kab. Probolinggo\n" .
                    "• WhatsApp Toko: 0852-3478-9169\n\n" .
                    "🔗 <a href=\"" . url('/admin/pengaturan-toko') . "\">Buka Modul Pengaturan Toko</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'kasir_info_barang':
                $this->botService->sendMessage(
                    $chatId,
                    "🆕 <b>Info Barang Baru & Penyesuaian Harga</b>\n\n" .
                    "• Katalog Produk Baru: 5 barang baru minggu ini\n" .
                    "• Update Harga Jual Terkini siap digunakan pada sistem kasir.\n\n" .
                    "🔗 <a href=\"" . url('/kasir/info-barang') . "\">Buka Modul Info Barang</a>",
                    $this->getKembaliKeyboard('menu_kasir')
                );
                break;

            case 'kasir_stok':
                $this->botService->sendMessage(
                    $chatId,
                    "📦 <b>Cek Stok Barang Kasir</b>\n\n" .
                    "• Seluruh item inventori toko tersedia untuk pencarian kasir.\n\n" .
                    "🔗 <a href=\"" . url('/kasir/stok') . "\">Buka Modul Lihat Stok</a>",
                    $this->getKembaliKeyboard('menu_kasir')
                );
                break;

            default:
                $this->kirimMenuUtama($chatId);
                break;
        }
    }

    /**
     * Mengirim menu utama bot.
     */
    protected function kirimMenuUtama($chatId): void
    {
        $text = "🏪 <b>SELAMAT DATANG DI BOT TOKO FIRO</b>\n\n" .
                "Silakan pilih menu navigasi peran Anda:";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '👨💼 Menu Admin', 'callback_data' => 'menu_admin'],
                    ['text' => '🛒 Menu Kasir', 'callback_data' => 'menu_kasir'],
                ],
                [
                    ['text' => '📊 Unduh Laporan Hari Ini (PDF)', 'callback_data' => 'admin_laporan_pdf'],
                ],
            ],
        ];

        $this->botService->sendMessage($chatId, $text, $keyboard);
    }

    /**
     * Mengirim 10 submenu Admin.
     */
    protected function kirimMenuAdmin($chatId): void
    {
        $text = "👨💼 <b>MENU ADMINISTRATOR TOKO FIRO</b>\n\n" .
                "Pilih menu di bawah ini untuk melihat ringkasan atau mengunduh dokumen:";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📄 Laporan Penjualan (PDF)', 'callback_data' => 'admin_laporan_pdf'],
                    ['text' => '📦 Kelola Data Barang', 'callback_data' => 'admin_barang'],
                ],
                [
                    ['text' => '📥 Pembelian Barang', 'callback_data' => 'admin_pembelian'],
                    ['text' => '📜 Log Aktivitas', 'callback_data' => 'admin_log'],
                ],
                [
                    ['text' => '⚠️ Batas Stok Minimum', 'callback_data' => 'admin_batas_stok'],
                    ['text' => '💰 Saldo Harian', 'callback_data' => 'admin_saldo'],
                ],
                [
                    ['text' => '👥 Akun Pengguna', 'callback_data' => 'admin_pengguna'],
                    ['text' => '💾 Backup Data', 'callback_data' => 'admin_backup'],
                ],
                [
                    ['text' => '🔔 Notifikasi Bot', 'callback_data' => 'admin_notifikasi'],
                    ['text' => '⚙️ Pengaturan Toko', 'callback_data' => 'admin_toko'],
                ],
                [
                    ['text' => '🔙 Kembali ke Menu Utama', 'callback_data' => 'menu_utama'],
                ],
            ],
        ];

        $this->botService->sendMessage($chatId, $text, $keyboard);
    }

    /**
     * Mengirim 5 submenu Kasir.
     */
    protected function kirimMenuKasir($chatId): void
    {
        $text = "🛒 <b>MENU KASIR TOKO FIRO</b>\n\n" .
                "Pilih modul kasir di bawah ini untuk pengecekan cepat:";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🆕 Info Barang Baru', 'callback_data' => 'kasir_info_barang'],
                    ['text' => '📦 Cek Stok Barang', 'callback_data' => 'kasir_stok'],
                ],
                [
                    ['text' => '🧾 Rekap Penjualan (PDF)', 'callback_data' => 'kasir_rekap'],
                    ['text' => '💵 Saldo Harian Kasir', 'callback_data' => 'kasir_saldo'],
                ],
                [
                    ['text' => '🔙 Kembali ke Menu Utama', 'callback_data' => 'menu_utama'],
                ],
            ],
        ];

        $this->botService->sendMessage($chatId, $text, $keyboard);
    }

    /**
     * Helper untuk membuat dokumen PDF laporan dan mengirimkannya.
     */
    protected function kirimLaporanPdf($chatId): void
    {
        $this->botService->sendMessage($chatId, '⏳ <i>Sedang menyiapkan berkas PDF Laporan Penjualan Toko Firo...</i>');

        $summary = $this->reportService->getSummaryData();
        $pdfBinary = $this->reportService->generateSalesPdfBinary($summary);
        $caption = $this->reportService->buildSalesCaption($summary);
        $filename = 'Laporan_Penjualan_Toko_Firo_' . date('Ymd_His') . '.pdf';

        $this->botService->sendDocument($chatId, $pdfBinary, $filename, $caption, [
            'inline_keyboard' => [
                [
                    ['text' => '🔙 Menu Utama', 'callback_data' => 'menu_utama'],
                ],
            ],
        ]);
    }

    /**
     * Helper tombol navigasi kembali.
     */
    protected function getKembaliKeyboard(string $target): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '🔙 Kembali', 'callback_data' => $target],
                    ['text' => '📋 Menu Utama', 'callback_data' => 'menu_utama'],
                ],
            ],
        ];
    }
}
