<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Menangani verifikasi peran (role) pengguna secara fleksibel dan mendukung hierarki akses.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Jika belum login, arahkan ke login (HTTP 302) atau 401 jika JSON
        if (! $user) {
            if ($request->expectsJson()) {
                abort(401, 'Unauthenticated.');
            }

            return redirect()->guest(route('login'));
        }

        // Periksa status keaktifan akun pengguna di tengah sesi (logout paksa jika NONAKTIF)
        $userStatus = strtoupper((string) ($user->status ?? ''));
        if (method_exists($user, 'fresh')) {
            $freshUser = $user->fresh();
            if ($freshUser) {
                $userStatus = strtoupper((string) ($freshUser->status ?? ''));
            }
        }

        if ($userStatus !== 'AKTIF') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                abort(403, 'Akun Anda telah dinonaktifkan.');
            }

            return redirect()->route('login')->withErrors([
                'login' => 'Akun Anda dinonaktifkan. Silakan hubungi administrator.',
            ]);
        }

        // Parse dan normalisasi seluruh peran yang diizinkan (mendukung pemisah koma / pipe)
        $allowedRoles = [];
        foreach ($roles as $roleParam) {
            foreach (preg_split('/[,|]/', (string) $roleParam) as $part) {
                $clean = strtolower(trim($part));
                if ($clean !== '') {
                    $allowedRoles[] = $clean;
                }
            }
        }

        $userRole = strtolower(trim((string) ($user->peran ?? $user->role ?? '')));

        // Hierarki & Whitelist Akses:
        // Pengguna dengan peran 'admin' atau 'pemilik' otomatis berhak mengakses operasional kasir
        $isKasirFeature = in_array('kasir', $allowedRoles, true);
        $isAdminOrPemilik = in_array($userRole, ['admin', 'pemilik'], true);

        $response = null;
        if ($isAdminOrPemilik && $isKasirFeature) {
            $response = $next($request);
        } elseif (in_array($userRole, $allowedRoles, true)) {
            $response = $next($request);
        } elseif ($userRole === 'pemilik' && in_array('admin', $allowedRoles, true)) {
            $response = $next($request);
        } else {
            abort(403, 'Akses tidak diizinkan untuk peran akun ini.');
        }

        if ($response instanceof Response) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
