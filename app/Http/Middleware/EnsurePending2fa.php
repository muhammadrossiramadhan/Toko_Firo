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
            return redirect()->route('demo.barang');
        }

        if (! $request->session()->has('pending_2fa_user_id')) {
            return redirect()->route('masuk');
        }

        return $next($request);
    }
}
