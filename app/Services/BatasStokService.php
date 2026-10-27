<?php

namespace App\Services;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class BatasStokService
{
    /**
     * Mengambil daftar item terpaginasi dengan filter jenis dan pencarian kata kunci.
     */
    public function getItems(?string $jenis = null, ?string $keyword = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = DB::table('daftar_item');

        if (!empty($jenis)) {
            $query->where('jenis', $jenis);
        }

        if (!empty($keyword)) {
            $sanitized = trim($keyword);
            $query->where(function ($q) use ($sanitized) {
                $q->where('nama_item', 'like', "%{$sanitized}%")
                  ->orWhere('kode_item', 'like', "%{$sanitized}%");
            });
        }

        $query->orderBy('kode_item', 'asc');

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->setCollection($paginator->getCollection()->map(function ($row) {
            $stok = (int) ($row->stok ?? 0);
            $batas = (int) ($row->batas_minimum ?? 0);

            if ($stok <= 0) {
                $status = 'Habis';
            } elseif ($stok <= $batas) {
                $status = 'Menipis';
            } elseif ($stok <= $batas * 2) {
                $status = 'Normal';
            } else {
                $status = 'Aman';
            }

            // Pasok properti kompatibilitas view dan standar DB
            $row->kode = $row->kode_item;
            $row->nama = $row->nama_item;
            $row->satuan = $row->satuan ?: 'pcs';
            $row->batas = $batas;
            $row->status = $status;

            return $row;
        }));

        return $paginator;
    }

    /**
     * Mengambil daftar unik jenis item untuk opsi dropdown filter.
     */
    public function getJenisList(): array
    {
        return DB::table('daftar_item')
            ->whereNotNull('jenis')
            ->where('jenis', '!=', '')
            ->distinct()
            ->orderBy('jenis', 'asc')
            ->pluck('jenis')
            ->toArray();
    }

    /**
     * Memperbarui batas stok minimum item tertentu via Stored Procedure.
     */
    public function updateBatasItem(string $kodeItem, int $nilai, ?int $idPelaku = null): bool
    {
        if ($nilai < 0) {
            throw new InvalidArgumentException('Batas minimum tidak boleh negatif.');
        }

        DB::statement('CALL sp_set_batas_minimum(?, ?, ?)', [
            $kodeItem,
            $nilai,
            $idPelaku,
        ]);

        // Hook Notifikasi Telegram: Kirim alert jika stok item saat ini <= batas minimum baru
        try {
            $item = DB::table('daftar_item')->where('kode_item', $kodeItem)->first();
            if ($item && (int) $item->stok <= $nilai) {
                $adminChatId = config('services.telegram.admin_chat_id') ?: config('services.telegram.chat_id');
                if (!empty($adminChatId)) {
                    $stokTerkini = (int) $item->stok;
                    $pesan = "⚠️ <b>PERINGATAN STOK MENIPIS</b>\n\n" .
                             "<b>Barang:</b> {$item->nama_item} ({$kodeItem})\n" .
                             "<b>Sisa Stok:</b> {$stokTerkini} {$item->satuan}\n" .
                             "<b>Batas Minimum:</b> {$nilai} {$item->satuan}\n" .
                             "<i>Segera lakukan restock atau pemesanan ke distributor.</i>";
                    app(TelegramBotService::class)->sendMessage($adminChatId, $pesan);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Telegram stock warning hook error: ' . $e->getMessage());
        }

        return true;
    }

    /**
     * Mengirim notifikasi peringatan stok minimum sebuah item ke Telegram.
     */
    public function kirimPeringatanStokMinimum(
        string $kodeItem,
        ?string $namaBarang = null,
        ?int $stokSekarang = null,
        ?int $batasMinimum = null,
        string $satuan = 'pcs'
    ): bool {
        // Cek toggle notifikasi stok_minimum (atau notif_stok_minimum)
        $notifAktif = true;
        try {
            $val = DB::table('pengaturan_notifikasi')
                ->whereIn('kunci', ['stok_minimum', 'notif_stok_minimum'])
                ->orderByRaw("CASE WHEN kunci = 'stok_minimum' THEN 1 ELSE 2 END")
                ->value('nilai');

            if ($val === null) {
                $jsonPath = storage_path('app/settings/notifications.json');
                if (file_exists($jsonPath)) {
                    $json = json_decode((string) file_get_contents($jsonPath), true);
                    if (isset($json['stok_minimum'])) {
                        $val = $json['stok_minimum'] ? '1' : '0';
                    }
                }
            }

            if ($val !== null) {
                $notifAktif = in_array(strtolower((string) $val), ['1', 'true', 'on', 'yes'], true);
            }
        } catch (\Throwable $e) {
            $notifAktif = true;
        }

        if (!$notifAktif) {
            return false;
        }

        $chatId = config('services.telegram.admin_chat_id')
            ?? config('services.telegram.chat_id')
            ?? (app()->environment('testing') ? null : (env('TELEGRAM_ADMIN_CHAT_ID') ?? env('TELEGRAM_GROUP_CHAT_ID')));

        if (empty($chatId)) {
            Log::warning('Peringatan stok minimum dibatalkan: Chat ID Telegram belum diatur.');
            return false;
        }

        if ($namaBarang === null || $stokSekarang === null || $batasMinimum === null) {
            $item = DB::table('daftar_item')->where('kode_item', $kodeItem)->first();
            if ($item) {
                $namaBarang = $namaBarang ?? $item->nama_item;
                $stokSekarang = $stokSekarang ?? (int) $item->stok;
                $batasMinimum = $batasMinimum ?? (int) ($item->batas_minimum ?? 5);
                $satuan = $item->satuan ?: $satuan;
            } else {
                $namaBarang = $namaBarang ?? $kodeItem;
                $stokSekarang = $stokSekarang ?? 0;
                $batasMinimum = $batasMinimum ?? 0;
            }
        }

        $pesan = "PERINGATAN STOK MINIMUM\n\n" .
                 "Barang: {$namaBarang} ({$kodeItem})\n" .
                 "Sisa Stok: {$stokSekarang} {$satuan}\n" .
                 "Batas Minimum: {$batasMinimum}\n\n" .
                 "Harap segera lakukan pengadaan/restock ulang barang ini!";

        try {
            /** @var TelegramBotService $bot */
            $bot = app(TelegramBotService::class);
            $res = $bot->kirimPesanTeks($chatId, $pesan);

            if ($res === false) {
                Log::error("Gagal mengirim peringatan stok minimum untuk item {$kodeItem} ke Telegram.");
                return false;
            }

            Log::info("Peringatan stok minimum berhasil dikirim ke Telegram untuk item {$kodeItem}.");
            return true;
        } catch (\Throwable $e) {
            Log::error('Error saat mengirim peringatan stok minimum ke Telegram: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Alias kompatibilitas untuk kirimPeringatanStokMinimum.
     */
    public function kirimPeringatanStokItem(
        string $kodeItem,
        ?string $namaBarang = null,
        ?int $stokSekarang = null,
        ?int $batasMinimum = null,
        string $satuan = 'pcs'
    ): bool {
        return $this->kirimPeringatanStokMinimum($kodeItem, $namaBarang, $stokSekarang, $batasMinimum, $satuan);
    }

    /**
     * Memeriksa seluruh item dengan stok kritis dan mengirimkan alert ringkasan ke Telegram.
     */
    public function kirimPeringatanStokKritis(): int
    {
        $kritis = DB::table('daftar_item')
            ->where(function ($q) {
                $q->where('tipe_item', '!=', 'JASA')->orWhereNull('tipe_item');
            })
            ->whereRaw('stok <= batas_minimum')
            ->get();

        if ($kritis->isEmpty()) {
            return 0;
        }

        $adminChatId = config('services.telegram.admin_chat_id') ?: config('services.telegram.chat_id');
        if (!empty($adminChatId)) {
            try {
                $text = "⚠️ <b>PERINGATAN STOK MENIPIS (" . $kritis->count() . " ITEM)</b>\n\n";
                foreach ($kritis->take(10) as $item) {
                    $text .= "• <b>{$item->nama_item}</b>: sisa {$item->stok} (min: {$item->batas_minimum})\n";
                }
                if ($kritis->count() > 10) {
                    $text .= "<i>...dan " . ($kritis->count() - 10) . " item lainnya.</i>\n";
                }
                app(TelegramBotService::class)->sendMessage($adminChatId, $text);
            } catch (\Throwable $e) {
                Log::warning('Telegram batch stock warning error: ' . $e->getMessage());
            }
        }

        return $kritis->count();
    }

    /**
     * Memperbarui batas stok minimum semua item non-jasa secara massal via Stored Procedure.
     */
    public function updateBatasSemua(int $nilai, ?int $idPelaku = null): bool
    {
        if ($nilai < 0) {
            throw new InvalidArgumentException('Batas minimum tidak boleh negatif.');
        }

        DB::statement('CALL sp_set_batas_minimum_semua(?, ?)', [
            $nilai,
            $idPelaku,
        ]);

        return true;
    }
}

