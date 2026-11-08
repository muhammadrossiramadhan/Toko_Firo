<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            $role = strtoupper(Auth::user()->role ?? '');
            if ($role === 'ADMIN') {
                return redirect()->route('admin.dashboard');
            } elseif ($role === 'KASIR') {
                return redirect()->route('kasir.dashboard');
            }
            // Role tidak dikenal — tetap arahkan ke kasir sebagai fallback aman
            return redirect()->route('kasir.dashboard');
        }

        return response()
            ->view('masuk')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function authenticate(Request $request, OtpService $otp, TelegramService $telegram)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'login' => 'Username atau password salah.',
            ])->onlyInput('username');
        }

        // Validasi status akun (SEC-04)
        if (strtoupper((string) ($user->status ?? '')) !== 'AKTIF') {
            return back()->withErrors([
                'login' => 'Akun Anda dinonaktifkan. Silakan hubungi administrator.',
            ])->onlyInput('username');
        }

        // Validasi cooldown kirim ulang OTP 90 detik (SEC-11)
        if (! $otp->canResend($user->id)) {
            return back()->withErrors([
                'login' => 'Tunggu 90 detik sebelum meminta kode baru.',
            ])->onlyInput('username');
        }

        // Step 1 passed — generate OTP, DON'T login yet
        $code = $otp->generate($user->id);
        $request->session()->put('pending_2fa_user_id', $user->id);

        $sent = $telegram->send("<b>Kode Login</b>\nStaf: {$user->name} (@{$user->username})\nKode: <code>{$code}</code>\nBerlaku 5 menit");

        if (! $sent) {
            $request->session()->flash('telegram_warning', 'Kode verifikasi gagal dikirim ke Telegram. Hubungi pemilik toko.');
        }

        return redirect()->route('verifikasi');
    }
}
