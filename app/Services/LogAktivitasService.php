<?php

namespace App\Services;

use DateTime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class LogAktivitasService
{
    /**
     * Membangun base query log aktivitas dengan join pengguna dan filter.
     */
    protected function buildQuery(array $filter): Builder
    {
        $query = DB::table('log_aktivitas as la')
            ->leftJoin('pengguna as p', 'la.id_pengguna', '=', 'p.id_pengguna')
            ->select([
                'la.id_log',
                'la.waktu',
                'la.keterangan',
                'la.tipe_aksi',
                'la.id_pengguna',
                'p.nama as nama_pengguna',
            ]);

        // 1. Filter Tipe Aksi (Daftar putih / pemetaan aman)
        $rawTipe = (string) ($filter['tipe'] ?? $filter['tipe_aksi'] ?? '');
        $tipeInput = strtoupper(trim($rawTipe));

        if ($tipeInput !== '') {
            $kelompokTipe = [
                'BARANG' => ['ITEM_BARU', 'ITEM_DIUBAH', 'ITEM_DIHAPUS', 'BARANG'],
                'HARGA' => ['HARGA_DIUBAH', 'HARGA'],
                'LOGIN' => ['LOGIN'],
                'STOK' => ['BATAS_STOK', 'STOK'],
                'MODAL' => ['SALDO_DIATUR', 'MODAL'],
                'TRANSAKSI' => ['TRANSAKSI', 'PENJUALAN', 'PEMBELIAN', 'RETUR'],
                'BACKUP' => ['BACKUP'],
                'NOTIFIKASI' => ['NOTIFIKASI'],
            ];

            if (isset($kelompokTipe[$tipeInput])) {
                $query->whereIn('la.tipe_aksi', $kelompokTipe[$tipeInput]);
            } else {
                // Binding parameter aman untuk filter eksak
                $query->where('la.tipe_aksi', $tipeInput);
            }
        }

        // 2. Filter Rentang Tanggal (Validasi ketat format Y-m-d)
        $startDate = (string) ($filter['tanggal_mulai'] ?? $filter['start_date'] ?? '');
        $endDate = (string) ($filter['tanggal_akhir'] ?? $filter['end_date'] ?? '');

        if ($this->isValidDate($startDate)) {
            $query->whereDate('la.waktu', '>=', $startDate);
        }

        if ($this->isValidDate($endDate)) {
            $query->whereDate('la.waktu', '<=', $endDate);
        }

        // 3. Filter Kata Kunci (Parameter binding aman dari SQL injection)
        $rawKeyword = (string) ($filter['kata_kunci'] ?? $filter['keyword'] ?? $filter['q'] ?? '');
        $keyword = trim($rawKeyword);

        if ($keyword !== '') {
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('la.keterangan', 'like', '%' . $keyword . '%')
                  ->orWhere('p.nama', 'like', '%' . $keyword . '%');
            });
        }

        return $query->orderByDesc('la.waktu')->orderByDesc('la.id_log');
    }

    /**
     * Mengambil daftar log aktivitas terpaginasi (default 10 baris).
     */
    public function getLogs(array $filter, int $perPage = 10): LengthAwarePaginator
    {
        $paginator = $this->buildQuery($filter)->paginate($perPage)->withQueryString();

        // Transformasi item agar sesuai dengan kontrak view blade Figma P16
        $paginator->getCollection()->transform(function ($row) {
            $tipeAksi = strtoupper((string) $row->tipe_aksi);

            $tipeTampil = 'Modal';
            if (in_array($tipeAksi, ['TRANSAKSI', 'PENJUALAN', 'PEMBELIAN', 'RETUR'], true)) {
                $tipeTampil = 'Transaksi';
            } elseif (in_array($tipeAksi, ['BATAS_STOK', 'STOK'], true) || str_contains(strtolower($row->keterangan ?? ''), 'habis')) {
                $tipeTampil = 'Stok';
            } elseif (str_contains($tipeAksi, 'LOGIN')) {
                $tipeTampil = 'Login';
            } elseif (str_contains($tipeAksi, 'ITEM') || $tipeAksi === 'BARANG') {
                $tipeTampil = 'Barang';
            } elseif (str_contains($tipeAksi, 'HARGA')) {
                $tipeTampil = 'Harga';
            } elseif ($tipeAksi === 'SALDO_DIATUR' || $tipeAksi === 'MODAL') {
                $tipeTampil = 'Modal';
            }

            return (object) [
                'id_log' => $row->id_log,
                'waktu' => date('d/m/y H:i', strtotime($row->waktu)),
                'waktu_raw' => $row->waktu,
                'user' => $row->nama_pengguna ?: 'Sistem',
                'nama_pengguna' => $row->nama_pengguna ?: 'Sistem',
                'tipe' => $tipeTampil,
                'tipe_aksi' => $row->tipe_aksi,
                'detail' => $row->keterangan,
                'keterangan' => $row->keterangan,
            ];
        });

        return $paginator;
    }

    /**
     * Mengambil daftar opsi tipe aksi unik untuk filter dropdown.
     */
    public function getTipeAksiList(): array
    {
        $standar = ['LOGIN', 'TRANSAKSI', 'BARANG', 'HARGA', 'STOK', 'MODAL', 'BACKUP', 'NOTIFIKASI'];
        $dariDb = DB::table('log_aktivitas')
            ->distinct()
            ->whereNotNull('tipe_aksi')
            ->pluck('tipe_aksi')
            ->toArray();

        return array_values(array_unique(array_merge($standar, $dariDb)));
    }

    /**
     * Menghasilkan string berkas CSV murni sesuai kriteria filter.
     */
    public function eksporCsv(array $filter): string
    {
        $rows = $this->buildQuery($filter)->get();

        $lines = ["Waktu,Tipe Aksi,Keterangan"];

        foreach ($rows as $row) {
            $waktu = (string) $row->waktu;
            $tipe = (string) ($row->tipe_aksi ?? '-');
            $ket = (string) ($row->keterangan ?? '-');

            if (str_contains($ket, ',') || str_contains($ket, '"') || str_contains($ket, "\n") || str_contains($ket, "\r")) {
                $ket = '"' . str_replace('"', '""', $ket) . '"';
            }

            $lines[] = "{$waktu},{$tipe},{$ket}";
        }

        return implode("\r\n", $lines) . "\r\n";
    }

    /**
     * Memeriksa validitas format tanggal (Y-m-d).
     */
    private function isValidDate(?string $date): bool
    {
        if (! $date) {
            return false;
        }

        $d = DateTime::createFromFormat('Y-m-d', $date);

        return $d && $d->format('Y-m-d') === $date;
    }
}
