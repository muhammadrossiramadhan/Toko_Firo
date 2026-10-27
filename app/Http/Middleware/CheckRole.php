<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

        if ($isAdminOrPemilik && $isKasirFeature) {
            return $next($request);
        }

        // Izin langsung jika role pengguna ada di dalam daftar yang diizinkan
        if (in_array($userRole, $allowedRoles, true)) {
            return $next($request);
        }

        // Jika peran 'pemilik' mengakses rute khusus 'admin'
        if ($userRole === 'pemilik' && in_array('admin', $allowedRoles, true)) {
            return $next($request);
        }

        abort(403, 'Akses tidak diizinkan untuk peran akun ini.');
    }
}
