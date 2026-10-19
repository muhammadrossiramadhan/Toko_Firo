<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __invoke(Request $request, TelegramService $telegram)
    {
        $user = Auth::user();
        if ($user) {
            $telegram->send("<b>Staf Keluar Sistem</b>\nNama: {$user->name} (@{$user->username})\nWaktu: " . now()->format('d/m/Y H:i:s'));
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('masuk');
    }
}
