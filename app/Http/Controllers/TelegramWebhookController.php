<?php

namespace App\Http\Controllers;

use App\Services\PengaturanTokoService;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;

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
        // 1. Validasi Subnet IP Resmi Telegram (SEC-16)
        $telegramSubnets = ['149.154.160.0/20', '91.108.4.0/22'];
        $clientIp = (string) $request->ip();

        $isAllowedLocal = app()->environment('testing', 'local') && in_array($clientIp, ['127.0.0.1', '::1'], true);
        if (! $isAllowedLocal && ! IpUtils::checkIp($clientIp, $telegramSubnets)) {
            Log::warning("Telegram webhook rejected: client IP {$clientIp} tidak berada di subnet resmi Telegram.");
            return response()->json(['error' => 'Forbidden: Unauthorized IP address'], 403);
        }

        // 2. Validasi Secret Token (X-Telegram-Bot-Api-Secret-Token)
        $expectedSecret = (string) (config('services.telegram.webhook_secret') ?: env('TELEGRAM_WEBHOOK_SECRET', ''));
        $receivedSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token', '');

        if ($expectedSecret === '' || ! hash_equals($expectedSecret, $receivedSecret)) {
            Log::warning('Telegram webhook rejected: secret token tidak valid atau tidak disediakan.');
            return response()->json(['error' => 'Forbidden: Invalid secret token'], 403);
        }

        $update = $request->all();

        // Ambil Chat ID dari update (message atau callback_query)
        $chatId = $update['message']['chat']['id']
            ?? $update['callback_query']['message']['chat']['id']
            ?? $update['callback_query']['from']['id']
            ?? null;

        // 3. Whitelist Otorisasi Chat ID
        if (! $this->isAuthorizedChat($chatId)) {
            Log::warning("Telegram webhook rejected: chat ID {$chatId} tidak terdaftar di whitelist.");
            return response()->json(['error' => 'Forbidden: Unauthorized chat ID'], 403);
        }

        try {
            // 4. Penanganan Callback Query dari tombol Inline Keyboard
            if (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
                return response()->json(['status' => 'ok', 'type' => 'callback_query']);
            }

            // 5. Penanganan Pesan Teks
            if (isset($update['message'])) {
                $this->handleMessage($update['message']);
                return response()->json(['status' => 'ok', 'type' => 'message']);
            }

            return response()->json(['status' => 'ignored']);
        } catch (\Throwable $e) {
            Log::error('Telegram webhook processing error: ' . $e->getMessage(), [
                'exception' => $e,
                'update' => $update,
            ]);
            return response()->json(['status' => 'error', 'message' => 'Processed with fallback'], 200);
        }
    }

    /**
     * Memeriksa apakah chat ID terdaftar di whitelist (chat_id atau admin_chat_id).
     */
    protected function isAuthorizedChat($chatId): bool
    {
        if (empty($chatId)) {
            return false;
        }

        $allowedGroup = (string) (config('services.telegram.chat_id') ?: env('TELEGRAM_GROUP_CHAT_ID', env('TELEGRAM_CHAT_ID', '')));
        $allowedAdmin = (string) (config('services.telegram.admin_chat_id') ?: env('TELEGRAM_ADMIN_CHAT_ID', ''));

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

        // Ekstrak command utama jika diawali '/', buang '@username' bot dan ambil keyword awal
        $cleanCommand = null;
        if (str_starts_with($text, '/')) {
            $firstWord = explode(' ', $text, 2)[0];
            $cleanCommand = strtolower(explode('@', $firstWord)[0]);
        }

        // a. Penanganan Command Bot Telegram
        if ($cleanCommand !== null) {
            switch ($cleanCommand) {
                case '/start':
                case '/menu':
                    $this->kirimMenuUtama($chatId);
                    return;

                case '/help':
                case '/bantuan':
                    $this->kirimBantuan($chatId);
                    return;

                case '/laporan':
                case '/rekap':
                case '/laporan_penjualan':
                case '/rekap_harian':
                    $this->kirimLaporanPdf($chatId);
                    return;

                case '/menu_admin':
                    $this->kirimMenuAdmin($chatId);
                    return;

                case '/menu_kasir':
                    $this->kirimMenuKasir($chatId);
                    return;
            }
        }

        $lowerText = strtolower($text);

        // b. Deteksi kata kunci natural untuk unduh Laporan Penjualan
        if (
            str_contains($lowerText, 'laporan penjualan') ||
            str_contains($lowerText, 'laporan hari ini') ||
            str_contains($lowerText, 'rekap hari ini') ||
            str_contains($lowerText, 'rekap penjualan')
        ) {
            $this->kirimLaporanPdf($chatId);
            return;
        }

        // c. Pesan bantuan panduan default
        $this->kirimBantuan($chatId);
    }

    /**
     * Mengirim panduan bantuan operasional bot.
     */
    protected function kirimBantuan($chatId): void
    {
        $pesan = "📖 <b>PANDUAN PENGGUNAAN BOT TOKO FIRO</b>\n\n" .
                 "Berikut adalah perintah yang dapat Anda gunakan:\n\n" .
                 "• <b>/start</b> atau <b>/menu</b> : Menampilkan Menu Utama navigasi bot\n" .
                 "• <b>/help</b> atau <b>/bantuan</b> : Menampilkan panduan bantuan ini\n" .
                 "• <b>/laporan</b> atau <b>/rekap</b> : Mengunduh berkas Laporan Penjualan PDF hari ini\n" .
                 "• <b>/menu_admin</b> : Membuka pintasan menu Administrator\n" .
                 "• <b>/menu_kasir</b> : Membuka pintasan menu Kasir\n\n" .
                 "<i>Anda juga dapat mengetik kata kunci seperti \"laporan penjualan\" atau memilih tombol berikut:</i>";

        $this->botService->sendMessage($chatId, $pesan, [
            'inline_keyboard' => [
                [
                    ['text' => '📋 Menu Utama', 'callback_data' => 'menu_utama'],
                    ['text' => '📊 Unduh Laporan (PDF)', 'callback_data' => 'admin_laporan_pdf'],
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

            case 'bantuan':
            case 'help':
                $this->kirimBantuan($chatId);
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
                $totalBarang = DB::table('daftar_item')->count();
                $stokAman = DB::table('daftar_item')->whereRaw('stok >= batas_minimum')->count();
                $stokMenipis = DB::table('daftar_item')->whereRaw('stok > 0 AND stok < batas_minimum')->count();
                $stokHabis = DB::table('daftar_item')->where('stok', '<=', 0)->count();

                $this->botService->sendMessage(
                    $chatId,
                    "📦 <b>Kelola Data Barang Toko Firo</b>\n\n" .
                    "• Total Master Barang: <b>{$totalBarang} item</b>\n" .
                    "• Stok Aman: {$stokAman} item\n" .
                    "• Stok Menipis: {$stokMenipis} item\n" .
                    "• Stok Habis: {$stokHabis} item\n\n" .
                    "🔗 <a href=\"" . url('/admin/barang') . "\">Buka Modul Data Barang</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_pembelian':
                $totalPembelian = DB::table('daftar_pembelian')->count();
                $pembelianBulanIni = DB::table('daftar_pembelian')
                    ->whereDate('tanggal_transaksi', '>=', now()->startOfMonth()->toDateString())
                    ->count();
                $suppliers = DB::table('daftar_pembelian')
                    ->whereNotNull('nama_supplier')
                    ->where('nama_supplier', '!=', '')
                    ->distinct()
                    ->limit(3)
                    ->pluck('nama_supplier')
                    ->toArray();
                $supplierText = !empty($suppliers) ? implode(', ', $suppliers) : 'Belum ada transaksi supplier';

                $this->botService->sendMessage(
                    $chatId,
                    "📥 <b>Pembelian Barang (Restock)</b>\n\n" .
                    "• Total Riwayat Restock: <b>{$totalPembelian} transaksi</b>\n" .
                    "• Pembelian Bulan Ini: <b>{$pembelianBulanIni} transaksi</b>\n" .
                    "• Supplier: {$supplierText}\n\n" .
                    "🔗 <a href=\"" . url('/admin/pembelian') . "\">Buka Modul Pembelian</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_log':
                $today = date('Y-m-d');
                $logHariIni = DB::table('log_aktivitas')->whereDate('waktu', $today)->count();
                $lastLog = DB::table('log_aktivitas')->orderByDesc('waktu')->value('keterangan') ?? 'Belum ada catatan aktivitas';

                $this->botService->sendMessage(
                    $chatId,
                    "📜 <b>Log Aktivitas Sistem</b>\n\n" .
                    "• Terakhir: {$lastLog}\n" .
                    "• Total Catatan Hari Ini: <b>{$logHariIni} aktivitas</b>\n\n" .
                    "🔗 <a href=\"" . url('/admin/log-aktivitas') . "\">Buka Modul Log Aktivitas</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_batas_stok':
                $avgMin = (int) round(DB::table('daftar_item')->avg('batas_minimum') ?: 5);
                $itemPerluRestock = DB::table('daftar_item')->whereRaw('stok <= batas_minimum')->count();

                $this->botService->sendMessage(
                    $chatId,
                    "⚠️ <b>Batas Stok Minimum</b>\n\n" .
                    "• Ambang Batas Rata-rata: Minimal {$avgMin} pcs\n" .
                    "• Barang Perlu Restock Cepat: <b>{$itemPerluRestock} item</b>\n\n" .
                    "🔗 <a href=\"" . url('/admin/batas-stok') . "\">Buka Modul Batas Stok</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_saldo':
            case 'kasir_saldo':
                $today = date('Y-m-d');
                $saldoHariIni = DB::table('saldo_harian')->where('tanggal', $today)->first();
                $saldoAwal = $saldoHariIni ? (float) ($saldoHariIni->saldo_awal ?? 0) : 0;
                $tunai = (float) DB::table('daftar_penjualan')
                    ->whereDate('tanggal_transaksi', $today)
                    ->whereRaw('UPPER(COALESCE(metode_bayar, "TUNAI")) = ?', ['TUNAI'])
                    ->sum('total_bayar');
                $nonTunai = (float) DB::table('daftar_penjualan')
                    ->whereDate('tanggal_transaksi', $today)
                    ->whereRaw('UPPER(COALESCE(metode_bayar, "TUNAI")) != ?', ['TUNAI'])
                    ->sum('total_bayar');
                $totalTransaksi = DB::table('daftar_penjualan')->whereDate('tanggal_transaksi', $today)->count();
                $totalSaldoKas = $saldoAwal + $tunai;

                $this->botService->sendMessage(
                    $chatId,
                    "💰 <b>Saldo Kasir Hari Ini</b>\n\n" .
                    "• Modal Awal: Rp " . number_format($saldoAwal, 0, ',', '.') . "\n" .
                    "• Penerimaan Tunai: Rp " . number_format($tunai, 0, ',', '.') . "\n" .
                    "• Penerimaan Non-Tunai / QRIS: Rp " . number_format($nonTunai, 0, ',', '.') . "\n" .
                    "• <b>Total Saldo Kas Fisik: Rp " . number_format($totalSaldoKas, 0, ',', '.') . "</b> ({$totalTransaksi} transaksi)\n\n" .
                    "🔗 <a href=\"" . url('/kasir/saldo') . "\">Buka Modul Saldo Kasir</a>",
                    $this->getKembaliKeyboard($data === 'admin_saldo' ? 'menu_admin' : 'menu_kasir')
                );
                break;

            case 'admin_pengguna':
                $totalPengguna = DB::table('pengguna')->count();
                $adminCount = DB::table('pengguna')->whereIn(DB::raw('UPPER(role)'), ['ADMIN', 'PEMILIK'])->count();
                $kasirCount = DB::table('pengguna')->where(DB::raw('UPPER(role)'), 'KASIR')->where(DB::raw('UPPER(status)'), 'AKTIF')->count();

                $this->botService->sendMessage(
                    $chatId,
                    "👥 <b>Kelola Akun Pengguna</b>\n\n" .
                    "• Total Pengguna: <b>{$totalPengguna} akun</b>\n" .
                    "• {$adminCount} Akun Admin / Pemilik\n" .
                    "• {$kasirCount} Akun Kasir Aktif\n\n" .
                    "🔗 <a href=\"" . url('/admin/pengguna') . "\">Buka Modul Pengguna</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_backup':
                $totalBackup = DB::table('riwayat_backup')->count();
                $lastBackup = DB::table('riwayat_backup')->where('status', 'BERHASIL')->orderByDesc('waktu')->first();
                $lastTime = $lastBackup ? Carbon::parse($lastBackup->waktu)->locale('id')->diffForHumans() : 'Belum pernah';

                $this->botService->sendMessage(
                    $chatId,
                    "💾 <b>Pencadangan Data (Backup)</b>\n\n" .
                    "• Total Cadangan: <b>{$totalBackup} arsip</b>\n" .
                    "• Terakhir Berhasil: {$lastTime}\n" .
                    "• Proteksi: Enkripsi ZIP AES-256\n\n" .
                    "🔗 <a href=\"" . url('/admin/backup') . "\">Buka Modul Backup</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_notifikasi':
                $notifStok = DB::table('pengaturan_notifikasi')->where('kunci', 'notif_stok_minimum')->value('nilai');
                $notifLaporan = DB::table('pengaturan_notifikasi')->where('kunci', 'laporan_otomatis')->value('nilai');
                $statusStok = ($notifStok === '0' || $notifStok === 'false') ? 'Nonaktif' : 'Aktif';
                $statusLaporan = ($notifLaporan === '0' || $notifLaporan === 'false') ? 'Nonaktif' : 'Aktif';

                $this->botService->sendMessage(
                    $chatId,
                    "🔔 <b>Pengaturan Notifikasi Bot</b>\n\n" .
                    "• Webhook Bot: <b>Aktif & Terhubung</b>\n" .
                    "• Notifikasi Rekap Penjualan: {$statusLaporan}\n" .
                    "• Notifikasi Stok Kritis: {$statusStok}\n\n" .
                    "🔗 <a href=\"" . url('/admin/pengaturan-notifikasi') . "\">Buka Modul Notifikasi</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'admin_toko':
                $tokoService = app(PengaturanTokoService::class);
                $namaToko = $tokoService->getNamaToko();
                $alamat = $tokoService->getAlamat();
                $telepon = $tokoService->getTelepon();

                $this->botService->sendMessage(
                    $chatId,
                    "⚙️ <b>Pengaturan Profil Toko</b>\n\n" .
                    "• Toko: {$namaToko}\n" .
                    "• Alamat: {$alamat}\n" .
                    "• Telepon / WhatsApp: {$telepon}\n\n" .
                    "🔗 <a href=\"" . url('/admin/pengaturan-toko') . "\">Buka Modul Pengaturan Toko</a>",
                    $this->getKembaliKeyboard('menu_admin')
                );
                break;

            case 'kasir_info_barang':
                $totalItem = DB::table('daftar_item')->count();
                $barangBaruCount = DB::table('daftar_item')
                    ->whereDate('dibuat_pada', '>=', now()->subDays(7)->toDateString())
                    ->count();

                $this->botService->sendMessage(
                    $chatId,
                    "🆕 <b>Info Barang Baru & Penyesuaian Harga</b>\n\n" .
                    "• Total Master Barang: {$totalItem} item\n" .
                    "• Produk Baru (7 hari): <b>{$barangBaruCount} barang baru</b>\n" .
                    "• Update harga jual terkini siap digunakan pada sistem kasir.\n\n" .
                    "🔗 <a href=\"" . url('/kasir/info-barang') . "\">Buka Modul Info Barang</a>",
                    $this->getKembaliKeyboard('menu_kasir')
                );
                break;

            case 'kasir_stok':
                $totalItem = DB::table('daftar_item')->count();
                $stokMenipis = DB::table('daftar_item')->whereRaw('stok <= batas_minimum')->count();

                $this->botService->sendMessage(
                    $chatId,
                    "📦 <b>Cek Stok Barang Kasir</b>\n\n" .
                    "• Total Barang: <b>{$totalItem} item</b>\n" .
                    "• Perhatian Stok Menipis: <b>{$stokMenipis} item</b>\n\n" .
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
     * Mengirimkan pesan ringkasan teks terlebih dahulu secara instan, lalu meng-offload pembuatan & pengiriman berkas PDF.
     */
    protected function kirimLaporanPdf($chatId): void
    {
        $summary = $this->reportService->getSummaryData();
        $caption = $this->reportService->buildSalesCaption($summary);

        $this->botService->sendMessage($chatId, "⏳ <i>Menyiapkan dokumen PDF Laporan Penjualan...</i>\n\n" . $caption);

        $sendPdfTask = function () use ($chatId, $summary, $caption) {
            try {
                $pdfBinary = $this->reportService->generateSalesPdfBinary($summary);
                $filename = 'Laporan_Penjualan_Toko_Firo_' . date('Ymd_His') . '.pdf';

                $this->botService->sendDocument($chatId, $pdfBinary, $filename, $caption, [
                    'inline_keyboard' => [
                        [
                            ['text' => '🔙 Menu Utama', 'callback_data' => 'menu_utama'],
                        ],
                    ],
                ]);
            } catch (\Throwable $e) {
                Log::error('Gagal membuat atau mengirim PDF laporan Telegram: ' . $e->getMessage());
            }
        };

        if (app()->environment('testing')) {
            $sendPdfTask();
        } else {
            app()->terminating($sendPdfTask);
        }
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
