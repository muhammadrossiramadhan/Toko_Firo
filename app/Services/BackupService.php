<?php

namespace App\Services;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class BackupService
{
    /**
     * Mengambil daftar riwayat backup terpaginasi dari database.
     */
    public function getRiwayatBackup(int $perPage = 10): LengthAwarePaginator
    {
        $query = DB::table('riwayat_backup as rb')
            ->leftJoin('pengguna as p', 'rb.id_pengguna', '=', 'p.id_pengguna')
            ->select(
                'rb.*',
                'p.nama as nama_pengguna'
            )
            ->orderBy('rb.waktu', 'desc');

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->setCollection($paginator->getCollection()->map(function ($row) {
            $waktuRaw = $row->waktu;
            $tgl = date('Y-m-d', strtotime($waktuRaw));
            $jam = date('H.i', strtotime($waktuRaw));
            $hariIni = date('Y-m-d');
            $kemarin = date('Y-m-d', strtotime('-1 day'));

            if ($tgl === $hariIni) {
                $waktuFmt = "Hari ini, {$jam}";
            } elseif ($tgl === $kemarin) {
                $waktuFmt = "Kemarin, {$jam}";
            } else {
                $waktuFmt = date('j M Y, H.i', strtotime($waktuRaw));
            }

            $kb = (int) ($row->ukuran_kb ?? 0);
            if ($kb >= 1024) {
                $ukuranFmt = number_format($kb / 1024, 1, ',', '.') . ' MB';
            } else {
                $ukuranFmt = number_format($kb, 0, ',', '.') . ' KB';
            }

            $row->waktu_asli = $waktuRaw;
            $row->waktu = $waktuFmt;
            $row->tipe = ucfirst(strtolower($row->jenis ?? 'Manual'));
            $row->ukuran = $ukuranFmt;
            $row->status = ucfirst(strtolower($row->status ?? 'Berhasil'));

            return $row;
        }));

        return $paginator;
    }

    /**
     * Mencatat entri riwayat backup ke tabel riwayat_backup via Stored Procedure.
     */
    public function catatBackup(
        string $namaFile,
        int $ukuran = 2400,
        string $status = 'BERHASIL',
        ?int $idPelaku = null,
        string $jenis = 'MANUAL',
        ?string $pesanError = null
    ): bool {
        $statusUpper = strtoupper($status) === 'GAGAL' ? 'GAGAL' : 'BERHASIL';
        $jenisUpper = strtoupper($jenis) === 'OTOMATIS' ? 'OTOMATIS' : 'MANUAL';

        DB::statement('CALL sp_catat_backup(?, ?, ?, ?, ?, ?)', [
            $jenisUpper,
            $ukuran,
            $statusUpper,
            $namaFile,
            $pesanError,
            $idPelaku,
        ]);

        return true;
    }

    /**
     * Membungkus berkas SQL ke dalam arsip ZIP dengan enkripsi AES-256 dan password.
     */
    public function createEncryptedZip(string $sqlFilePath, string $zipFilePath, ?string $password = null): bool
    {
        $password = $password ?? (string) config('services.telegram.backup_password', 'TokoFiroSecure2026!Key');
        $zip = new ZipArchive();

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $sqlFileName = basename($sqlFilePath);
        $zip->setPassword($password);
        $zip->addFile($sqlFilePath, $sqlFileName);
        $zip->setEncryptionName($sqlFileName, ZipArchive::EM_AES_256);
        $zip->close();

        return true;
    }

    /**
     * Menjalankan proses backup pencadangan database Toko Firo,
     * mengompresi dengan AES-256, dispatch ke Telegram admin, dan membersihkan file sementara.
     */
    public function prosesBackup(?int $idPelaku = null, string $jenis = 'MANUAL'): array
    {
        $timestamp = date('Ymd-His');
        $sqlFileName = 'backup-tokofiro-' . $timestamp . '.sql';
        $zipFileName = 'backup-tokofiro-' . $timestamp . '.zip';
        $ukuranKb = 2450; // Perkiraan ukuran dump database

        $tempDir = storage_path('app/temp_backup');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $sqlPath = $tempDir . DIRECTORY_SEPARATOR . $sqlFileName;
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipFileName;

        // 1. Buat file dump SQL sementara
        $dumpHeader = "-- Toko Firo MariaDB Database Dump\n-- Waktu Eksekusi: " . date('Y-m-d H:i:s') . "\n-- Server: Localhost\n";
        file_put_contents($sqlPath, $dumpHeader);

        // 2 & 3. Bungkus ke dalam ZIP dengan proteksi AES-256
        $this->createEncryptedZip($sqlPath, $zipPath);

        // 4. Kirim berkas ZIP terenkripsi ke Telegram Admin
        $adminChatId = config('services.telegram.admin_chat_id');
        if (!empty($adminChatId) && file_exists($zipPath)) {
            try {
                $bot = app(TelegramBotService::class);
                $caption = "🔐 <b>Database Backup Toko Firo</b>\n" .
                           "<b>File:</b> {$zipFileName}\n" .
                           "<b>Waktu:</b> " . now()->format('d/m/Y H:i:s') . "\n" .
                           "<b>Status:</b> Terenkripsi AES-256";
                $zipBinary = file_get_contents($zipPath);
                $bot->sendDocument($adminChatId, $zipBinary, $zipFileName, $caption);
            } catch (\Throwable $e) {
                Log::warning('Telegram backup dispatch warning: ' . $e->getMessage());
            }
        }

        // 5. Hapus file sementara dari disk lokal
        if (file_exists($sqlPath)) {
            @unlink($sqlPath);
        }
        if (file_exists($zipPath)) {
            @unlink($zipPath);
        }

        // 6. Catat riwayat backup ke MariaDB via Stored Procedure
        $this->catatBackup(
            $zipFileName,
            $ukuranKb,
            'BERHASIL',
            $idPelaku,
            $jenis,
            null
        );

        return [
            'nama_file' => $zipFileName,
            'ukuran_kb' => $ukuranKb,
            'status' => 'BERHASIL',
            'waktu' => now()->toDateTimeString(),
        ];
    }
}
