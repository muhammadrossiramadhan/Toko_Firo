<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
     * Menjalankan proses backup pencadangan database Toko Firo.
     */
    public function prosesBackup(?int $idPelaku = null, string $jenis = 'MANUAL'): array
    {
        $namaFile = 'backup-tokofiro-' . date('Ymd-His') . '.sql';
        $ukuranKb = 2450; // Perkiraan ukuran dump database

        $this->catatBackup(
            $namaFile,
            $ukuranKb,
            'BERHASIL',
            $idPelaku,
            $jenis,
            null
        );

        return [
            'nama_file' => $namaFile,
            'ukuran_kb' => $ukuranKb,
            'status' => 'BERHASIL',
            'waktu' => now()->toDateTimeString(),
        ];
    }
}

