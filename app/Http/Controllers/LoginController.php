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
        return Auth::check() ? redirect()->route('demo.barang') : view('masuk');
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
