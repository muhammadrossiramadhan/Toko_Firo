<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses permintaan login.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cari Pengguna berdasarkan username
        $pengguna = Pengguna::where('username', $credentials['username'])->first();

        // Jika pengguna ditemukan dan berstatus NONAKTIF
        if ($pengguna && $pengguna->status === 'NONAKTIF') {
            return back()->withInput($request->only('username'))->withErrors([
                'login' => 'Akun Anda tidak aktif. Hubungi pemilik toko.',
            ]);
        }

        // Percobaan autentikasi dengan username dan password
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Perbarui kolom login_terakhir
            /** @var \App\Models\Pengguna $user */
            $user = Auth::user();
            $user->update([
                'login_terakhir' => now(),
            ]);

            // TODO: Route /admin/barang belum ada (dibuat di L6), sesuaikan tujuan redirect setelah route tersedia.
            return redirect()->intended('/');
        }

        // Pesan umum jika autentikasi gagal (tidak membedakan username salah atau password salah)
        return back()->withInput($request->only('username'))->withErrors([
            'login' => 'Username atau password salah.',
        ]);
    }

    /**
     * Memproses logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

