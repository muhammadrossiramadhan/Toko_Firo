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
            return redirect()->guest(route('login'));
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

        // Hierarki: admin dan pemilik berhak akses operasional kasir
        $isKasirFeature = in_array('kasir', $allowedRoles, true);
        $isAdminOrPemilik = in_array($userRole, ['admin', 'pemilik'], true);

        if ($isAdminOrPemilik && $isKasirFeature) {
            return $next($request);
        }

        if (in_array($userRole, $allowedRoles, true)) {
            return $next($request);
        }

        if ($userRole === 'pemilik' && in_array('admin', $allowedRoles, true)) {
            return $next($request);
        }

        abort(403, 'Akses tidak diizinkan untuk peran akun ini.');
    }
}
