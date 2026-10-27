<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePending2fa
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $role = strtoupper(Auth::user()->role ?? '');
            if ($role === 'ADMIN') {
                return redirect()->route('admin.dashboard');
            } elseif ($role === 'KASIR') {
                return redirect()->route('kasir.dashboard');
            }
            return redirect('/');
        }

        if (! $request->session()->has('pending_2fa_user_id')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
