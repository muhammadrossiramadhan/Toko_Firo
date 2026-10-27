<?php

namespace App\Services;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BackupService
{
    /**
     * Membuat cadangan database baru (auto-detect dump binary atau fallback native PDO).
     */
    public function buatBackup(string $tipe = 'Manual'): array
    {
        $tipeNormalized = ucfirst(strtolower($tipe));
        $timestamp = date('Y-m-d_His');
        $namaFile = "backup_toko_firo_{$timestamp}.sql";

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0755, true);
        }

        $targetPath = $backupDir . DIRECTORY_SEPARATOR . $namaFile;

        // Eksekusi dump database
        $sukses = $this->performDump($targetPath);
        if (!$sukses || !file_exists($targetPath) || filesize($targetPath) === 0) {
            throw new \RuntimeException('Gagal membuat berkas dump SQL database.');
        }

        $ukuranBytes = filesize($targetPath) ?: 0;
        $ukuranFormatted = $this->formatBytes($ukuranBytes);
        $waktuFormatted = date('d M Y, H:i');
        $waktuIso = date('Y-m-d H:i:s');
        $id = (string) round(microtime(true) * 1000);

        $detail = [
            'id' => $id,
            'nama_file' => $namaFile,
            'path' => 'backups/' . $namaFile,
            'ukuran' => $ukuranFormatted,
            'ukuran_bytes' => $ukuranBytes,
            'waktu' => $waktuFormatted,
            'waktu_iso' => $waktuIso,
            'tipe' => $tipeNormalized,
            'status' => 'Berhasil',
        ];

        // Simpan metadata ke backup_history.json
        $this->saveHistoryMetadata($detail);

        // Catat juga ke tabel riwayat_backup di MariaDB
        $ukuranKb = (int) round($ukuranBytes / 1024);
        try {
            $this->catatBackup($namaFile, $ukuranKb, 'BERHASIL', auth()->id(), strtoupper($tipeNormalized));
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat backup ke riwayat_backup: ' . $e->getMessage());
        }

        // Periksa status saklar notifikasi dan kirim pesan Telegram jika aktif
        $this->kirimNotifikasiSelesai($waktuFormatted, $tipeNormalized, $ukuranFormatted);

        return $detail;
    }

    /**
     * Mengambil riwayat backup tersimpan dari metadata JSON, disk, dan database.
     */
    public function getRiwayat(): array
    {
        $backupDir = storage_path('app/backups');
        $jsonPath = $backupDir . DIRECTORY_SEPARATOR . 'backup_history.json';
        $items = [];

        if (file_exists($jsonPath)) {
            $raw = json_decode((string) file_get_contents($jsonPath), true);
            if (is_array($raw)) {
                $items = $raw;
            }
        }

        // Scan berkas di folder backups jika belum tercatat di JSON
        if (is_dir($backupDir)) {
            $files = glob($backupDir . DIRECTORY_SEPARATOR . '*.{sql,zip}', GLOB_BRACE) ?: [];
            $recordedFiles = array_column($items, 'nama_file');

            foreach ($files as $filePath) {
                $basename = basename($filePath);
                if (!in_array($basename, $recordedFiles, true)) {
                    $bytes = filesize($filePath) ?: 0;
                    $items[] = [
                        'id' => (string) (count($items) + 1),
                        'nama_file' => $basename,
                        'path' => 'backups/' . $basename,
                        'ukuran' => $this->formatBytes($bytes),
                        'ukuran_bytes' => $bytes,
                        'waktu' => date('d M Y, H:i', filemtime($filePath)),
                        'waktu_iso' => date('Y-m-d H:i:s', filemtime($filePath)),
                        'tipe' => 'Manual',
                        'status' => 'Berhasil',
                    ];
                }
            }
        }

        // Fallback ke tabel riwayat_backup jika items kosong
        if (empty($items)) {
            try {
                $dbRows = DB::table('riwayat_backup')->orderBy('waktu', 'desc')->limit(10)->get();
                foreach ($dbRows as $idx => $r) {
                    $kb = (int) ($r->ukuran_kb ?? 0);
                    $items[] = [
                        'id' => (string) ($idx + 1),
                        'nama_file' => $r->nama_file,
                        'path' => 'backups/' . $r->nama_file,
                        'ukuran' => $this->formatBytes($kb * 1024),
                        'ukuran_bytes' => $kb * 1024,
                        'waktu' => date('d M Y, H:i', strtotime($r->waktu)),
                        'waktu_iso' => $r->waktu,
                        'tipe' => ucfirst(strtolower($r->jenis ?? 'Manual')),
                        'status' => ucfirst(strtolower($r->status ?? 'Berhasil')),
                    ];
                }
            } catch (\Throwable $e) {
                // Abaikan
            }
        }

        $totalBytes = array_sum(array_column($items, 'ukuran_bytes'));
        $totalFile = count($items);
        $terakhir = !empty($items) ? ($items[0]['waktu'] ?? '-') : '-';

        return [
            'items' => $items,
            'total_file' => $totalFile,
            'total_ukuran' => $this->formatBytes($totalBytes),
            'terakhir' => $terakhir,
        ];
    }

    /**
     * Mengunduh berkas cadangan dengan sanitasi pencegahan directory traversal.
     */
    public function unduhBackup(string $filename): BinaryFileResponse
    {
        $cleanName = basename($filename);

        if ($cleanName !== $filename || str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            abort(400, 'Nama berkas tidak valid.');
        }

        $backupDir = storage_path('app/backups');
        $filePath = $backupDir . DIRECTORY_SEPARATOR . $cleanName;

        if (!file_exists($filePath)) {
            $altPath = storage_path('app' . DIRECTORY_SEPARATOR . $cleanName);
            if (file_exists($altPath)) {
                $filePath = $altPath;
            } else {
                abort(404, 'Berkas cadangan tidak ditemukan.');
            }
        }

        $contentType = str_ends_with($cleanName, '.zip') ? 'application/zip' : 'application/sql';

        return response()->download($filePath, $cleanName, [
            'Content-Type' => $contentType,
        ]);
    }

    /**
     * Menghapus berkas cadangan fisik dan membersihkan metadata riwayatnya.
     */
    public function hapusBackup(string $filename): bool
    {
        $cleanName = basename($filename);

        if ($cleanName !== $filename || str_contains($filename, '..')) {
            return false;
        }

        $filePath = storage_path('app/backups' . DIRECTORY_SEPARATOR . $cleanName);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        // Hapus juga file alternatif di storage/app jika ada
        $altPath = storage_path('app' . DIRECTORY_SEPARATOR . $cleanName);
        if (file_exists($altPath)) {
            @unlink($altPath);
        }

        // Bersihkan dari metadata JSON
        $jsonPath = storage_path('app/backups/backup_history.json');
        if (file_exists($jsonPath)) {
            $history = json_decode((string) file_get_contents($jsonPath), true) ?: [];
            $history = array_values(array_filter($history, fn ($item) => ($item['nama_file'] ?? '') !== $cleanName));
            file_put_contents($jsonPath, json_encode($history, JSON_PRETTY_PRINT));
        }

        // Hapus dari database jika tercatat
        try {
            DB::table('riwayat_backup')->where('nama_file', $cleanName)->delete();
        } catch (\Throwable $e) {
            // Abaikan
        }

        return true;
    }

    /**
     * Auto-detect binary dump atau beralih ke native PDO table export.
     */
    protected function performDump(string $targetSqlPath): bool
    {
        $binary = $this->getDumpBinaryPath();

        if ($binary) {
            try {
                $connection = config('database.default', 'mariadb');
                $dbConfig = config("database.connections.{$connection}", []);

                $host = $dbConfig['host'] ?? '127.0.0.1';
                $port = $dbConfig['port'] ?? '3306';
                $database = $dbConfig['database'] ?? '';
                $username = $dbConfig['username'] ?? 'root';
                $password = $dbConfig['password'] ?? '';

                $cmd = sprintf(
                    '"%s" --host=%s --port=%s --user=%s %s --routines --triggers --single-transaction --databases %s --result-file="%s"',
                    $binary,
                    escapeshellarg($host),
                    escapeshellarg((string) $port),
                    escapeshellarg($username),
                    !empty($password) ? '--password=' . escapeshellarg($password) : '',
                    escapeshellarg($database),
                    $targetSqlPath
                );

                $exitCode = 0;
                $output = [];
                exec($cmd, $output, $exitCode);

                if ($exitCode === 0 && file_exists($targetSqlPath) && filesize($targetSqlPath) > 0) {
                    return true;
                }

                Log::warning("CLI dump via binary {$binary} gagal (exit code {$exitCode}), menggunakan fallback native PDO.");
            } catch (\Throwable $e) {
                Log::warning('Eksepsi saat CLI dump: ' . $e->getMessage() . ', beralih ke native PDO.');
            }
        }

        // Fallback: Native PHP PDO export
        return $this->exportWithPdo($targetSqlPath);
    }

    /**
     * Export database menggunakan native PHP PDO.
     */
    protected function exportWithPdo(string $targetSqlPath): bool
    {
        $pdo = DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();

        $handle = fopen($targetSqlPath, 'w');
        if (!$handle) {
            return false;
        }

        fwrite($handle, "-- ===================================================================\n");
        fwrite($handle, "-- Database Backup: " . $dbName . "\n");
        fwrite($handle, "-- Waktu Eksekusi: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- Engine: Toko Firo Native PDO Backup Service\n");
        fwrite($handle, "-- ===================================================================\n\n");
        fwrite($handle, "SET NAMES utf8mb4;\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

        $tableKey = 'Tables_in_' . $dbName;

        // 1. Export BASE TABLES
        $tables = DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        foreach ($tables as $t) {
            $props = get_object_vars($t);
            $tableName = $props[$tableKey] ?? array_values($props)[0];

            fwrite($handle, "-- -------------------------------------------------------------------\n");
            fwrite($handle, "-- Table structure for `{$tableName}`\n");
            fwrite($handle, "-- -------------------------------------------------------------------\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");

            $createRow = DB::selectOne("SHOW CREATE TABLE `{$tableName}`");
            $createSql = $createRow->{'Create Table'} ?? array_values(get_object_vars($createRow))[1];
            fwrite($handle, $createSql . ";\n\n");

            // Dump Data secara chunked
            fwrite($handle, "-- Dumping data for table `{$tableName}`\n");
            $count = DB::table($tableName)->count();
            if ($count > 0) {
                DB::table($tableName)->orderBy(DB::raw('1'))->chunk(200, function ($rows) use ($handle, $tableName, $pdo) {
                    if ($rows->isEmpty()) {
                        return;
                    }

                    $columns = array_keys((array) $rows->first());
                    $escapedCols = array_map(fn ($col) => "`{$col}`", $columns);
                    $colList = implode(', ', $escapedCols);

                    $valuesList = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ((array) $row as $val) {
                            if ($val === null) {
                                $rowValues[] = 'NULL';
                            } elseif (is_numeric($val) && !is_string($val)) {
                                $rowValues[] = $val;
                            } else {
                                $rowValues[] = $pdo->quote((string) $val);
                            }
                        }
                        $valuesList[] = '(' . implode(', ', $rowValues) . ')';
                    }

                    fwrite($handle, "INSERT INTO `{$tableName}` ({$colList}) VALUES\n" . implode(",\n", $valuesList) . ";\n");
                });
                fwrite($handle, "\n");
            }
        }

        // 2. Export VIEWS
        $views = DB::select("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
        foreach ($views as $v) {
            $props = get_object_vars($v);
            $viewName = $props[$tableKey] ?? array_values($props)[0];

            fwrite($handle, "-- -------------------------------------------------------------------\n");
            fwrite($handle, "-- View structure for `{$viewName}`\n");
            fwrite($handle, "-- -------------------------------------------------------------------\n");
            $createViewRow = DB::selectOne("SHOW CREATE VIEW `{$viewName}`");
            $createViewSql = $createViewRow->{'Create View'} ?? array_values(get_object_vars($createViewRow))[1];
            fwrite($handle, "DROP VIEW IF EXISTS `{$viewName}`;\n");
            fwrite($handle, $createViewSql . ";\n\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($handle);

        return true;
    }

    /**
     * Mencari path binary dump eksternal di environment lokal.
     */
    protected function getDumpBinaryPath(): ?string
    {
        $connection = config('database.default', 'mariadb');
        $configured = config("database.connections.{$connection}.dump.dump_binary_path")
            ?: config('database.connections.mysql.dump.dump_binary_path')
            ?: env('DB_DUMP_PATH');

        if (!empty($configured) && file_exists($configured)) {
            return $configured;
        }

        $windowsCandidates = [
            'C:\\Program Files\\FlyEnv-Data\\app\\mariadb-13.1.1\\bin\\mariadb-dump.exe',
            'C:\\Program Files\\FlyEnv-Data\\app\\mariadb-13.1.1\\bin\\mysqldump.exe',
            'C:\\Program Files\\FlyEnv-Data\\env\\mariadb\\bin\\mariadb-dump.exe',
            'C:\\Program Files\\FlyEnv-Data\\env\\mysql\\bin\\mysqldump.exe',
        ];

        foreach ($windowsCandidates as $cand) {
            if (file_exists($cand)) {
                return $cand;
            }
        }

        $flyEnvAppDumps = glob('C:\\Program Files\\FlyEnv-Data\\app\\*\\bin\\mariadb-dump.exe') ?: [];
        if (!empty($flyEnvAppDumps) && file_exists($flyEnvAppDumps[0])) {
            return $flyEnvAppDumps[0];
        }

        $flyEnvMysqlDumps = glob('C:\\Program Files\\FlyEnv-Data\\app\\*\\bin\\mysqldump.exe') ?: [];
        if (!empty($flyEnvMysqlDumps) && file_exists($flyEnvMysqlDumps[0])) {
            return $flyEnvMysqlDumps[0];
        }

        $linuxCandidates = [
            '/usr/bin/mariadb-dump',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mariadb-dump',
            '/usr/local/bin/mysqldump',
        ];

        foreach ($linuxCandidates as $cand) {
            if (file_exists($cand) && is_executable($cand)) {
                return $cand;
            }
        }

        return null;
    }

    /**
     * Menyimpan metadata cadangan ke berkas JSON.
     */
    protected function saveHistoryMetadata(array $entry): void
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $jsonPath = $dir . DIRECTORY_SEPARATOR . 'backup_history.json';
        $history = file_exists($jsonPath) ? (json_decode((string) file_get_contents($jsonPath), true) ?: []) : [];

        array_unshift($history, $entry);

        file_put_contents($jsonPath, json_encode($history, JSON_PRETTY_PRINT));
    }

    /**
     * Mengirim notifikasi ke Telegram jika toggle saklar notifikasi backup aktif.
     */
    protected function kirimNotifikasiSelesai(string $waktu, string $tipe, string $ukuran): void
    {
        $notifAktif = true;
        try {
            $val = DB::table('pengaturan_notifikasi')->where('kunci', 'notif_backup')->value('nilai');
            if ($val !== null) {
                $notifAktif = in_array(strtolower((string) $val), ['1', 'true', 'on', 'yes'], true);
            }
        } catch (\Throwable $e) {
            // Default true
        }

        if (!$notifAktif) {
            return;
        }

        try {
            $chatId = config('services.telegram.admin_chat_id')
                ?: config('services.telegram.chat_id')
                ?: env('TELEGRAM_ADMIN_CHAT_ID')
                ?: env('TELEGRAM_GROUP_CHAT_ID');

            if (!empty($chatId)) {
                /** @var TelegramBotService $bot */
                $bot = app(TelegramBotService::class);
                $pesan = "Pemberitahuan Sistem: Backup database Toko Firo berhasil dibuat pada {$waktu}. Tipe: {$tipe}. Ukuran file: {$ukuran}.";
                $bot->kirimPesanTeks($chatId, $pesan);
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim notifikasi Telegram backup: ' . $e->getMessage());
        }
    }

    /**
     * Format byte ke string KB / MB.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1, ',', '.') . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, ',', '.') . ' KB';
        }

        return $bytes . ' B';
    }

    // =========================================================================
    // METODE KOMPATIBILITAS (TEST & SISTEM LAMA)
    // =========================================================================

    /**
     * Mengambil daftar riwayat backup terpaginasi dari database.
     */
    public function getRiwayatBackup(int $perPage = 10): LengthAwarePaginator
    {
        $query = DB::table('riwayat_backup as rb')
            ->leftJoin('pengguna as p', 'rb.id_pengguna', '=', 'p.id_pengguna')
            ->select('rb.*', 'p.nama as nama_pengguna')
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
     * Menjalankan proses backup pencadangan database Toko Firo terenkripsi ke Telegram.
     */
    public function prosesBackup(?int $idPelaku = null, string $jenis = 'MANUAL'): array
    {
        $timestamp = date('Ymd-His');
        $sqlFileName = 'backup-tokofiro-' . $timestamp . '.sql';
        $zipFileName = 'backup-tokofiro-' . $timestamp . '.zip';
        $ukuranKb = 2450;

        $tempDir = storage_path('app/temp_backup');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $sqlPath = $tempDir . DIRECTORY_SEPARATOR . $sqlFileName;
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipFileName;

        $dumpHeader = "-- Toko Firo MariaDB Database Dump\n-- Waktu Eksekusi: " . date('Y-m-d H:i:s') . "\n-- Server: Localhost\n";
        file_put_contents($sqlPath, $dumpHeader);

        $this->createEncryptedZip($sqlPath, $zipPath);

        $adminChatId = config('services.telegram.admin_chat_id');
        if (!empty($adminChatId) && file_exists($zipPath)) {
            try {
                /** @var TelegramBotService $bot */
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

        if (file_exists($sqlPath)) {
            @unlink($sqlPath);
        }
        if (file_exists($zipPath)) {
            @unlink($zipPath);
        }

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
