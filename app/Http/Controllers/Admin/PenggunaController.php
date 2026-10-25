<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PenggunaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        // Penanganan aksi jika dipanggil melalui POST atau parameter query
        if ($request->isMethod('post') || $request->has('action')) {
            $action = $request->input('action');
            if ($action === 'store' || $request->isMethod('post')) {
                return $this->store($request);
            }
            if ($action === 'toggle_status') {
                return $this->toggleStatus($request, (int) $request->input('id_pengguna'));
            }
            if ($action === 'reset_password') {
                return $this->resetPassword($request, (int) $request->input('id_pengguna'));
            }
        }

        $role = $request->query('role');
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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'min:3', 'max:100'],
            'username' => ['required', 'string', 'min:4', 'max:50', 'unique:pengguna,username'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string', 'in:ADMIN,KASIR,admin,kasir'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.min' => 'Nama lengkap minimal 3 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'role.in' => 'Role pengguna harus ADMIN atau KASIR.',
        ]);

        $idPelaku = auth()->id() ?? 1;

        $this->penggunaService->createPengguna(
            $validated['nama'],
            $validated['username'],
            $validated['password'],
            strtoupper($validated['role']),
            $idPelaku
        );

        return redirect()->route('admin.pengguna')
            ->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    /**
     * Mengubah data profil pengguna.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'min:3', 'max:100'],
            'username' => ['required', 'string', 'min:4', 'max:50', 'unique:pengguna,username,' . $id . ',id_pengguna'],
            'role' => ['required', 'string', 'in:ADMIN,KASIR,admin,kasir'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'role.required' => 'Role pengguna wajib dipilih.',
        ]);

        $idPelaku = auth()->id() ?? 1;

        $this->penggunaService->updatePengguna(
            $id,
            $validated['nama'],
            $validated['username'],
            strtoupper($validated['role']),
            $idPelaku
        );

        return redirect()->route('admin.pengguna')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Mengubah status aktif/nonaktif akun pengguna.
     */
    public function toggleStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:AKTIF,NONAKTIF,aktif,nonaktif'],
        ], [
            'status.required' => 'Status wajib ditentukan.',
            'status.in' => 'Status tidak valid.',
        ]);

        $idPelaku = auth()->id() ?? 1;

        $this->penggunaService->setStatus(
            $id,
            strtoupper($validated['status']),
            $idPelaku
        );

        return redirect()->route('admin.pengguna')
            ->with('success', 'Status pengguna berhasil diperbarui.');
    }

    /**
     * Mengatur ulang password akun pengguna.
     */
    public function resetPassword(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
        ]);

        $idPelaku = auth()->id() ?? 1;

        $this->penggunaService->resetPassword(
            $id,
            $validated['password'],
            $idPelaku
        );

        return redirect()->route('admin.pengguna')
            ->with('success', 'Password pengguna berhasil diatur ulang.');
    }
}
