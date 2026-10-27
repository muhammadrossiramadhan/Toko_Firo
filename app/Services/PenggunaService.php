<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class PenggunaService
{
    /**
     * Mengambil daftar pengguna terpaginasi dengan filter role, status, dan pencarian kata kunci.
     */
    public function getPenggunaList(
        ?string $role = null,
        ?string $status = null,
        ?string $keyword = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = DB::table('pengguna');

        if (!empty($role) && in_array(strtoupper($role), ['ADMIN', 'KASIR'])) {
            $query->where('role', strtoupper($role));
        }

        if (!empty($status) && in_array(strtoupper($status), ['AKTIF', 'NONAKTIF'])) {
            $query->where('status', strtoupper($status));
        }

        if (!empty($keyword)) {
            $sanitized = trim($keyword);
            $query->where(function ($q) use ($sanitized) {
                $q->where('nama', 'like', "%{$sanitized}%")
                  ->orWhere('username', 'like', "%{$sanitized}%");
            });
        }

        $query->orderBy('id_pengguna', 'desc');

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->setCollection($paginator->getCollection()->map(function ($item) {
            $item->inisial = strtoupper(substr($item->nama ?: $item->username, 0, 1));
            $item->login_terakhir = !empty($item->login_terakhir)
                ? date('d M Y, H:i', strtotime($item->login_terakhir))
                : '-';
            return $item;
        }));

        return $paginator;
    }

    /**
     * Menambahkan pengguna baru ke sistem via Stored Procedure.
     */
    public function createPengguna(
        string $nama,
        string $username,
        string $password,
        string $role,
        ?int $idPelaku = null
    ): bool {
        $roleUpper = strtoupper($role);
        if (!in_array($roleUpper, ['ADMIN', 'KASIR'])) {
            throw new InvalidArgumentException('Role tidak valid. Pilihan: ADMIN atau KASIR.');
        }

        if (strlen($username) < 4) {
            throw new InvalidArgumentException('Username minimal 4 karakter.');
        }

        if (strlen($password) < 6) {
            throw new InvalidArgumentException('Password minimal 6 karakter.');
        }

        DB::statement('CALL sp_create_pengguna(?, ?, ?, ?, ?)', [
            $nama,
            $username,
            Hash::make($password),
            $roleUpper,
            $idPelaku,
        ]);

        return true;
    }

    /**
     * Memperbarui profil akun pengguna via Stored Procedure.
     */
    public function updatePengguna(
        int $idPengguna,
        string $nama,
        string $username,
        string $role,
        ?int $idPelaku = null
    ): bool {
        $roleUpper = strtoupper($role);
        if (!in_array($roleUpper, ['ADMIN', 'KASIR'])) {
            throw new InvalidArgumentException('Role tidak valid. Pilihan: ADMIN atau KASIR.');
        }

        try {
            DB::statement('CALL sp_update_pengguna(?, ?, ?, ?, ?)', [
                $idPengguna,
                $nama,
                $username,
                $roleUpper,
                $idPelaku,
            ]);
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), '1267') || str_contains($e->getMessage(), 'collations')) {
                $user = DB::table('pengguna')->where('id_pengguna', $idPengguna)->first();
                if (!$user) {
                    throw new InvalidArgumentException('Pengguna tidak ditemukan!');
                }

                $exists = DB::table('pengguna')
                    ->where('username', $username)
                    ->where('id_pengguna', '!=', $idPengguna)
                    ->exists();

                if ($exists) {
                    throw new InvalidArgumentException('Username sudah dipakai!');
                }

                DB::table('pengguna')
                    ->where('id_pengguna', $idPengguna)
                    ->update([
                        'nama' => $nama,
                        'username' => $username,
                        'role' => $roleUpper,
                        'diubah_pada' => now(),
                    ]);

                DB::table('log_aktivitas')->insert([
                    'waktu' => now(),
                    'keterangan' => 'Update pengguna: ' . $username,
                    'tipe_aksi' => 'PENGGUNA_DIUBAH',
                    'id_pengguna' => $idPelaku,
                    'data_sebelum' => json_encode([
                        'nama' => $user->nama,
                        'username' => $user->username,
                        'role' => $user->role,
                    ]),
                    'data_sesudah' => json_encode([
                        'nama' => $nama,
                        'username' => $username,
                        'role' => $roleUpper,
                    ]),
                ]);
            } else {
                throw $e;
            }
        }

        return true;
    }

    /**
     * Mengubah status aktif/nonaktif akun pengguna via Stored Procedure.
     */
    public function setStatus(int $idPengguna, string $status, ?int $idPelaku = null): bool
    {
        $statusUpper = strtoupper($status);
        if (!in_array($statusUpper, ['AKTIF', 'NONAKTIF'])) {
            throw new InvalidArgumentException('Status tidak valid. Pilihan: AKTIF atau NONAKTIF.');
        }

        DB::statement('CALL sp_set_status_pengguna(?, ?, ?)', [
            $idPengguna,
            $statusUpper,
            $idPelaku,
        ]);

        return true;
    }

    /**
     * Mengatur ulang password akun pengguna via Stored Procedure.
     */
    public function resetPassword(int $idPengguna, string $passwordBaru, ?int $idPelaku = null): bool
    {
        if (strlen($passwordBaru) < 6) {
            throw new InvalidArgumentException('Password baru minimal 6 karakter.');
        }

        DB::statement('CALL sp_reset_password(?, ?, ?)', [
            $idPengguna,
            Hash::make($passwordBaru),
            $idPelaku,
        ]);

        return true;
    }

    /**
     * Menghapus akun pengguna via Stored Procedure (jika belum ada transaksi).
     */
    public function deletePengguna(int $idPengguna, ?int $idPelaku = null): bool
    {
        DB::statement('CALL sp_delete_pengguna(?, ?)', [
            $idPengguna,
            $idPelaku,
        ]);

        return true;
    }
}

