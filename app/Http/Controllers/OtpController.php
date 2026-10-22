<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class OtpController extends Controller
{
    public function showVerifyForm(Request $request)
    {
        $user = User::findOrFail($request->session()->get('pending_2fa_user_id'));
        return view('verifikasi', ['username' => $user->username]);
    }

    public function verify(Request $request, OtpService $otp)
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $userId = $request->session()->get('pending_2fa_user_id');
        $result = $otp->verify($userId, $request->code);

        if ($result === true) {
            $request->session()->forget('pending_2fa_user_id');
            $user = User::findOrFail($userId);
            Auth::login($user);
            $request->session()->regenerate();

            $role = strtoupper($user->role ?? '');
            if ($role === 'ADMIN') {
                return redirect()->intended(Route::has('admin.dashboard') ? route('admin.dashboard') : '/admin/dashboard');
            } elseif ($role === 'KASIR') {
                return redirect()->intended(Route::has('kasir.dashboard') ? route('kasir.dashboard') : '/kasir/dashboard');
            }

            return redirect()->intended('/');
        }

        if ($result === 'max_attempts') {
            $request->session()->forget('pending_2fa_user_id');
            return redirect()->route('masuk')->withErrors([
                'login' => 'Terlalu banyak percobaan salah, silakan login ulang.',
            ]);
        }

        $message = $result === 'expired'
            ? 'Kode sudah kedaluwarsa. Kirim ulang kode baru.'
            : 'Kode yang dimasukkan salah.';

        return back()->withErrors(['code' => $message]);
    }

    public function resend(Request $request, OtpService $otp, TelegramService $telegram)
    {
        $userId = $request->session()->get('pending_2fa_user_id');

        if (! $otp->canResend($userId)) {
            return back()->withErrors(['resend' => 'Tunggu 90 detik sebelum mengirim ulang.']);
        }

        $user = User::findOrFail($userId);
        $code = $otp->generate($userId);
        $telegram->send("<b>Kode Login Baru</b>\nStaf: {$user->name} (@{$user->username})\nKode: <code>{$code}</code>\nBerlaku 5 menit");

        return back()->with('resent', 'Kode baru telah dikirim ke grup Telegram.');
    }
}
