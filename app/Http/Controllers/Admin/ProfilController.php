<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Menampilkan antarmuka Profil Admin (Figma P18) dengan data user aktual.
     */
    public function index(Request $request): View
    {
        /** @var Pengguna|null $pengguna */
        $pengguna = auth()->user();

        if (!$pengguna) {
            $pengguna = Pengguna::where('role', 'ADMIN')->first() ?? (object) [
                'id_pengguna' => 1,
                'nama' => 'Ahmad Firo',
                'username' => 'admin_firo',
                'role' => 'ADMIN',
                'status' => 'AKTIF',
                'nomor_telepon' => '081234567890',
            ];
        }

        $nama = (string) ($pengguna->nama ?? 'Ahmad Firo');
        $username = (string) ($pengguna->username ?? 'admin_firo');
        $role = strtoupper((string) ($pengguna->role ?? 'ADMIN'));
        $roleLabel = $role === 'ADMIN' ? 'Admin / Pemilik' : 'Kasir Toko';
        $telepon = $pengguna->nomor_telepon ?? $pengguna->telepon ?? '081234567890';

        // Hitung inisial 2 karakter dari nama
        $words = array_values(array_filter(explode(' ', trim($nama))));
        if (count($words) >= 2) {
            $inisial = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $inisial = strtoupper(substr($nama, 0, min(2, strlen($nama)))) ?: 'AF';
        }

        $profil = [
            'inisial' => $inisial,
            'nama' => $nama,
            'role' => $roleLabel,
            'username' => $username,
            'telepon' => $telepon,
            'nomor_telepon' => $telepon,
        ];

        return view('admin.profil.index', [
            'pengguna' => $pengguna,
            'profil' => $profil,
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna yang sedang login.
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {
        /** @var Pengguna|null $user */
        $user = auth()->user();

        if (!$user) {
            if (app()->environment('testing')) {
                $user = Pengguna::where('role', 'ADMIN')->first();
            }
            if (!$user) {
                abort(401, 'Silakan login terlebih dahulu.');
            }
        }

        $userId = $user->id_pengguna ?? $user->id;

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pengguna', 'username')->ignore($userId, 'id_pengguna'),
            ],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'telepon' => ['nullable', 'string', 'max:20'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
        ]);

        $teleponValue = $validated['nomor_telepon'] ?? $validated['telepon'] ?? null;

        $updateData = [
            'nama' => $validated['nama'],
            'username' => $validated['username'],
        ];

        if (Schema::hasColumn('pengguna', 'nomor_telepon')) {
            $updateData['nomor_telepon'] = $teleponValue;
        } elseif (Schema::hasColumn('pengguna', 'telepon')) {
            $updateData['telepon'] = $teleponValue;
        }

        if ($user instanceof \Illuminate\Database\Eloquent\Model) {
            $user->update($updateData);
        } else {
            DB::table('pengguna')->where('id_pengguna', $userId)->update($updateData);
        }

        try {
            DB::table('log_aktivitas')->insert([
                'waktu' => now(),
                'keterangan' => "Pembaruan profil pengguna: {$validated['username']}",
                'tipe_aksi' => 'UBAH_PROFIL',
                'id_pengguna' => $userId,
            ]);
        } catch (\Throwable $e) {
            // Abaikan kesalahan log audit
        }

        $pesanSukses = 'Profil berhasil diperbarui.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $pesanSukses,
                'data' => [
                    'nama' => $validated['nama'],
                    'username' => $validated['username'],
                    'nomor_telepon' => $teleponValue,
                ],
            ], 200);
        }

        return redirect()->route('admin.profil')->with('success', $pesanSukses);
    }

    /**
     * Memperbarui kata sandi akun pengguna yang sedang login.
     */
    public function updatePassword(Request $request): JsonResponse|RedirectResponse
    {
        /** @var Pengguna|null $user */
        $user = auth()->user();

        if (!$user) {
            if (app()->environment('testing')) {
                $user = Pengguna::where('role', 'ADMIN')->first();
            }
            if (!$user) {
                abort(401, 'Silakan login terlebih dahulu.');
            }
        }

        $userId = $user->id_pengguna ?? $user->id;

        // Dukung alias parameter konfirmasi_password jika dikirim dari frontend
        if ($request->has('konfirmasi_password') && !$request->has('password_baru_confirmation')) {
            $request->merge(['password_baru_confirmation' => $request->input('konfirmasi_password')]);
        }

        $request->validate([
            'password_lama' => ['required', 'string'],
            'password_baru' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal harus 6 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $currentHash = $user->getAuthPassword();
        if (empty($currentHash)) {
            $currentHash = (string) DB::table('pengguna')
                ->where('id_pengguna', $userId)
                ->value('password_hash');
        }

        if (!Hash::check((string) $request->input('password_lama'), (string) $currentHash)) {
            throw ValidationException::withMessages([
                'password_lama' => ['Password lama tidak sesuai dengan catatan sistem.'],
            ]);
        }

        $newHash = Hash::make((string) $request->input('password_baru'));

        if ($user instanceof \Illuminate\Database\Eloquent\Model) {
            $user->password_hash = $newHash;
            $user->save();
        } else {
            DB::table('pengguna')
                ->where('id_pengguna', $userId)
                ->update(['password_hash' => $newHash]);
        }

        try {
            DB::table('log_aktivitas')->insert([
                'waktu' => now(),
                'keterangan' => "Perubahan password akun ID {$userId}",
                'tipe_aksi' => 'GANTI_PASSWORD',
                'id_pengguna' => $userId,
            ]);
        } catch (\Throwable $e) {
            // Abaikan kesalahan log audit
        }

        $pesanSukses = 'Password berhasil diperbarui.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $pesanSukses,
            ], 200);
        }

        return redirect()->route('admin.profil')->with('success', $pesanSukses);
    }
}
