<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function __construct(
        protected TelegramBotService $botService
    ) {}

    /**
     * Menampilkan antarmuka Pengaturan Notifikasi Bot (Figma P17).
     */
    public function index(Request $request): View
    {
        $botToken = (string) (
            config('services.telegram.bot_token')
            ?: (app()->environment('testing') ? '' : env('TELEGRAM_BOT_TOKEN', ''))
        );
        $chatId = (string) (
            config('services.telegram.admin_chat_id')
            ?: config('services.telegram.chat_id')
            ?: (app()->environment('testing') ? '' : (env('TELEGRAM_ADMIN_CHAT_ID') ?: env('TELEGRAM_GROUP_CHAT_ID', '')))
        );

        $isConnected = !empty($botToken) && !empty($chatId);

        if (!empty($botToken)) {
            $maskedToken = strlen($botToken) > 12
                ? substr($botToken, 0, 4) . '•••••••••••••••••:' . substr($botToken, -4)
                : '•••••••••••••••••:AAH...';
        } else {
            $maskedToken = 'Belum diatur';
        }

        $displayChatId = !empty($chatId) ? $chatId : 'Belum diatur';
        $status = $isConnected ? 'Terhubung' : 'Belum Terhubung';

        $telegramConfig = [
            'status' => $status,
            'is_connected' => $isConnected,
            'bot_token' => $maskedToken,
            'chat_id' => $displayChatId,
        ];

        // Baca pengaturan persisten dari database & storage JSON
        $dbSettings = [];
        try {
            $dbSettings = DB::table('pengaturan_notifikasi')->pluck('nilai', 'kunci')->all();
        } catch (\Throwable $e) {
            Log::warning('Gagal membaca tabel pengaturan_notifikasi: ' . $e->getMessage());
        }

        $jsonSettings = [];
        $jsonPath = storage_path('app/settings/notifications.json');
        if (file_exists($jsonPath)) {
            try {
                $jsonSettings = json_decode((string) file_get_contents($jsonPath), true) ?: [];
            } catch (\Throwable $e) {
                // Abaikan
            }
        }

        $getStatus = function (string $id, string $dbKey, bool $default = true) use ($dbSettings, $jsonSettings): bool {
            if (isset($dbSettings[$dbKey])) {
                $val = strtolower(trim((string) $dbSettings[$dbKey]));
                return in_array($val, ['1', 'true', 'on', 'yes'], true);
            }
            if (isset($jsonSettings[$id])) {
                return (bool) $jsonSettings[$id];
            }
            return $default;
        };

        $daftarPengaturan = [
            (object) [
                'id' => 'rekap_harian',
                'label' => 'Rekap Laporan Penjualan Harian (otomatis setiap pukul 21.00 WIB)',
                'aktif' => $getStatus('rekap_harian', 'laporan_otomatis', true),
            ],
            (object) [
                'id' => 'stok_minimum',
                'label' => 'Peringatan Stok Minimum & Habis (otomatis saat transaksi selesai)',
                'aktif' => $getStatus('stok_minimum', 'notif_stok_minimum', true),
            ],
            (object) [
                'id' => 'perubahan_item',
                'label' => 'Penambahan Item Baru & Perubahan Harga',
                'aktif' => $getStatus('perubahan_item', 'notif_item_baru', true),
            ],
            (object) [
                'id' => 'backup_selesai',
                'label' => 'Pemberitahuan Backup Database Selesai',
                'aktif' => $getStatus('backup_selesai', 'notif_backup', true),
            ],
            (object) [
                'id' => 'otp_2fa',
                'label' => 'Pengiriman Kode OTP 2FA untuk Login Staf',
                'aktif' => $getStatus('otp_2fa', 'notif_otp', true),
            ],
        ];

        $riwayatNotifikasi = [
            (object) [
                'waktu' => '03 Okt 2026, 21:00',
                'tipe' => 'Rekap Harian',
                'pesan' => 'Laporan penjualan harian — Total: Rp 2.450.000 (34 nota)',
                'status' => 'Terkirim',
            ],
            (object) [
                'waktu' => '03 Okt 2026, 15:32',
                'tipe' => 'Stok Habis',
                'pesan' => 'Pensil 2B (BRG-008) stok habis — segera restock!',
                'status' => 'Terkirim',
            ],
            (object) [
                'waktu' => '03 Okt 2026, 10:15',
                'tipe' => 'Stok Menipis',
                'pesan' => 'Isi Staples No.10 (BRG-006) tersisa 2 box — batas minimum 3',
                'status' => 'Terkirim',
            ],
            (object) [
                'waktu' => '02 Okt 2026, 21:00',
                'tipe' => 'Rekap Harian',
                'pesan' => 'Laporan penjualan harian — Total: Rp 1.890.000 (28 nota)',
                'status' => 'Gagal',
            ],
        ];

        return view('admin.notifikasi.index', [
            'telegramConfig' => $telegramConfig,
            'daftarPengaturan' => $daftarPengaturan,
            'riwayatNotifikasi' => $riwayatNotifikasi,
        ]);
    }

    /**
     * Mengirimkan pesan uji coba koneksi ke Telegram.
     */
    public function testKoneksi(Request $request): JsonResponse
    {
        try {
            $botToken = config('services.telegram.bot_token')
                ?: (app()->environment('testing') ? null : env('TELEGRAM_BOT_TOKEN'));

            $chatId = config('services.telegram.admin_chat_id')
                ?: config('services.telegram.chat_id')
                ?: (app()->environment('testing') ? null : (env('TELEGRAM_ADMIN_CHAT_ID') ?: env('TELEGRAM_GROUP_CHAT_ID')));

            if (empty($botToken) || empty($chatId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Konfigurasi Telegram (Bot Token atau Chat ID) belum diatur di sistem.',
                ], 500);
            }

            $pesan = "Tes Koneksi Toko Firo: Bot Telegram berhasil terhubung ke sistem.";
            $response = $this->botService->kirimPesanTeks($chatId, $pesan);

            if ($response === false || (is_array($response) && isset($response['ok']) && !$response['ok'])) {
                $errMsg = is_array($response) && isset($response['description'])
                    ? $response['description']
                    : 'Gagal mengirim pesan uji coba ke server Telegram.';

                throw new \Exception($errMsg);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pesan uji coba berhasil dikirim ke Telegram!',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Uji coba koneksi Telegram gagal: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim pesan uji coba: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menyimpan perubahan toggle pengaturan notifikasi.
     */
    public function simpan(Request $request): JsonResponse
    {
        $mapping = [
            'rekap_harian'   => 'laporan_otomatis',
            'stok_minimum'   => 'notif_stok_minimum',
            'perubahan_item' => 'notif_item_baru',
            'backup_selesai' => 'notif_backup',
            'otp_2fa'        => 'notif_otp',
        ];

        try {
            $updated = [];

            if ($request->has('id')) {
                $id = (string) $request->input('id');
                $aktif = filter_var($request->input('aktif'), FILTER_VALIDATE_BOOLEAN);

                if (!isset($mapping[$id])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'ID pengaturan notifikasi tidak valid.',
                    ], 422);
                }

                $dbKey = $mapping[$id];
                $this->persistToggle($id, $dbKey, $aktif);
                $updated[$id] = $aktif;
            } elseif ($request->has('toggles')) {
                $toggles = (array) $request->input('toggles');
                foreach ($toggles as $id => $aktif) {
                    if (isset($mapping[$id])) {
                        $dbKey = $mapping[$id];
                        $boolVal = filter_var($aktif, FILTER_VALIDATE_BOOLEAN);
                        $this->persistToggle($id, $dbKey, $boolVal);
                        $updated[$id] = $boolVal;
                    }
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pengaturan tidak ditemukan pada request.',
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pengaturan notifikasi berhasil disimpan.',
                'data' => $updated,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan pengaturan notifikasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan pengaturan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menyimpan status toggle ke persistent database dan file JSON.
     */
    protected function persistToggle(string $id, string $dbKey, bool $aktif): void
    {
        // 1. Simpan ke database
        try {
            DB::table('pengaturan_notifikasi')->updateOrInsert(
                ['kunci' => $dbKey],
                [
                    'nilai' => $aktif ? '1' : '0',
                    'diubah_pada' => now(),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning("Gagal update tabel pengaturan_notifikasi untuk {$dbKey}: " . $e->getMessage());
        }

        // 2. Simpan ke storage JSON sebagai persistent fallback
        try {
            $dir = storage_path('app/settings');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $file = $dir . '/notifications.json';
            $current = file_exists($file) ? json_decode((string) file_get_contents($file), true) : [];
            $current[$id] = $aktif;
            file_put_contents($file, json_encode($current, JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            Log::warning("Gagal simpan ke notifications.json: " . $e->getMessage());
        }
    }
}
