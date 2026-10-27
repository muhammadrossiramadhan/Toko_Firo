<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Services\PenggunaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function __construct(
        protected PenggunaService $penggunaService
    ) {}

    /**
     * Menampilkan halaman kelola akun pengguna (Figma P13).
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Penanganan aksi jika dipanggil melalui POST legacy
        if ($request->isMethod('post') && $request->has('action')) {
            $action = $request->input('action');
            if ($action === 'store') {
                return $this->store($request);
            }
            if ($action === 'toggle_status') {
                return $this->toggleStatus($request, (int) $request->input('id_pengguna'));
            }
            if ($action === 'reset_password') {
                return $this->resetPassword($request, (int) $request->input('id_pengguna'));
            }
            if ($action === 'destroy') {
                return $this->destroy($request, (int) $request->input('id_pengguna'));
            }
        }

        $role = $request->query('role') ?? $request->query('peran');
        $status = $request->query('status');
        $keyword = $request->query('q') ?? $request->query('keyword') ?? $request->query('cari');

        $pengguna = $this->penggunaService->getPenggunaList($role, $status, $keyword, 10);

        return view('admin.pengguna.index', [
            'pengguna' => $pengguna,
            'filterRole' => $role,
            'filterStatus' => $status,
            'keyword' => $keyword,
        ]);
    }

    /**
     * Menyimpan data pengguna baru ke database.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // Dukung alias nama atribut peran <-> role
        if ($request->has('role') && !$request->has('peran')) {
            $request->merge(['peran' => $request->input('role')]);
        }
        if ($request->has('peran') && !$request->has('role')) {
            $request->merge(['role' => $request->input('peran')]);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:pengguna,username'],
            'password' => ['required', 'string', 'min:6'],
            'peran' => ['required', 'string', 'in:admin,kasir,pemilik,ADMIN,KASIR,PEMILIK'],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'peran.required' => 'Peran pengguna wajib dipilih.',
            'peran.in' => 'Pilihan peran harus admin, kasir, atau pemilik.',
        ]);

        $idPelaku = auth()->id() ?? auth()->user()?->id_pengguna ?? 1;
        $roleDb = in_array(strtolower($validated['peran']), ['admin', 'pemilik'], true) ? 'ADMIN' : 'KASIR';

        $this->penggunaService->createPengguna(
            $validated['nama'],
            $validated['username'],
            $validated['password'],
            $roleDb,
            $idPelaku
        );

        $pesan = 'Akun pengguna berhasil ditambahkan.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $pesan,
            ], 201);
        }

        return redirect()->route('admin.pengguna.index')->with('success', $pesan);
    }

    /**
     * Mengubah data profil pengguna.
     */
    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        if ($request->has('role') && !$request->has('peran')) {
            $request->merge(['peran' => $request->input('role')]);
        }
        if ($request->has('peran') && !$request->has('role')) {
            $request->merge(['role' => $request->input('peran')]);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('pengguna', 'username')->ignore($id, 'id_pengguna')],
            'peran' => ['required', 'string', 'in:admin,kasir,pemilik,ADMIN,KASIR,PEMILIK'],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'peran.required' => 'Peran pengguna wajib dipilih.',
        ]);

        $idPelaku = auth()->id() ?? auth()->user()?->id_pengguna ?? 1;
        $roleDb = in_array(strtolower($validated['peran']), ['admin', 'pemilik'], true) ? 'ADMIN' : 'KASIR';

        $this->penggunaService->updatePengguna(
            $id,
            $validated['nama'],
            $validated['username'],
            $roleDb,
            $idPelaku
        );

        $pesan = 'Data pengguna berhasil diperbarui.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $pesan,
            ], 200);
        }

        return redirect()->route('admin.pengguna.index')->with('success', $pesan);
    }

    /**
     * Mengubah status aktif/nonaktif akun pengguna.
     */
    public function toggleStatus(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $currentUserId = auth()->id() ?? auth()->user()?->id_pengguna;

        // Cegah menonaktifkan akun sendiri
        if ((int) $id === (int) $currentUserId) {
            $pesan = 'Tidak dapat menonaktifkan akun yang sedang digunakan!';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $pesan,
                ], 422);
            }
            return redirect()->back()->with('error', $pesan);
        }

        $targetUser = DB::table('pengguna')->where('id_pengguna', $id)->first();
        if (!$targetUser) {
            abort(404, 'Pengguna tidak ditemukan.');
        }

        if ($request->has('status') && !empty($request->input('status'))) {
            $newStatus = strtoupper($request->input('status'));
        } else {
            $newStatus = strtoupper($targetUser->status ?? '') === 'AKTIF' ? 'NONAKTIF' : 'AKTIF';
        }

        if (!in_array($newStatus, ['AKTIF', 'NONAKTIF'], true)) {
            $newStatus = 'AKTIF';
        }

        $this->penggunaService->setStatus(
            $id,
            $newStatus,
            $currentUserId ?: 1
        );

        $pesan = 'Status pengguna berhasil diperbarui menjadi ' . ucfirst(strtolower($newStatus)) . '.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $pesan,
                'new_status' => $newStatus,
            ], 200);
        }

        return redirect()->route('admin.pengguna.index')->with('success', $pesan);
    }

    /**
     * Mengatur ulang password akun pengguna.
     */
    public function resetPassword(Request $request, int $id): JsonResponse|RedirectResponse
    {
        if ($request->has('password') && !$request->has('password_baru')) {
            $request->merge(['password_baru' => $request->input('password')]);
        }
        if ($request->has('password_baru') && !$request->has('password')) {
            $request->merge(['password' => $request->input('password_baru')]);
        }

        $validated = $request->validate([
            'password_baru' => ['required', 'string', 'min:6'],
        ], [
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal 6 karakter.',
        ]);

        $idPelaku = auth()->id() ?? auth()->user()?->id_pengguna ?? 1;

        $this->penggunaService->resetPassword(
            $id,
            $validated['password_baru'],
            $idPelaku
        );

        $pesan = 'Password pengguna berhasil diatur ulang.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $pesan,
            ], 200);
        }

        return redirect()->route('admin.pengguna.index')->with('success', $pesan);
    }

    /**
     * Menghapus akun pengguna dari database.
     */
    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $currentUserId = auth()->id() ?? auth()->user()?->id_pengguna;

        // Proteksi keamanan: Larang keras menghapus akun yang sedang login sendiri
        if ((int) $id === (int) $currentUserId) {
            $pesan = 'Tidak dapat menghapus akun yang sedang Anda gunakan saat ini!';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $pesan,
                ], 422);
            }
            return redirect()->back()->with('error', $pesan);
        }

        try {
            $this->penggunaService->deletePengguna($id, $currentUserId ?: 1);

            $pesan = 'Akun pengguna berhasil dihapus.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $pesan,
                ], 200);
            }

            return redirect()->route('admin.pengguna.index')->with('success', $pesan);
        } catch (\Throwable $e) {
            $errMsg = $e->getMessage();
            if (str_contains($errMsg, 'memiliki transaksi') || str_contains($errMsg, 'sudah memiliki')) {
                $errMsg = 'Akun tidak bisa dihapus karena sudah memiliki data riwayat transaksi penjualan/pembelian. Silakan nonaktifkan akun sebagai gantinya.';
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $errMsg,
                ], 422);
            }

            return redirect()->back()->with('error', $errMsg);
        }
    }
}
