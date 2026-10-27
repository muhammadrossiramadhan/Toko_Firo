<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Jika belum login: redirect ke route login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        // Jika status pengguna bukan AKTIF: logout, invalidate session, redirect ke login dengan pesan error
        if ($user->status !== 'AKTIF') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'login' => 'Akun Anda tidak aktif. Hubungi pemilik toko.',
            ]);
        }

        // Jika role pengguna tidak ada di $roles: abort(403)
        if (! in_array($user->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}

