<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Muat seluruh rute Admin terisolasi
            foreach (glob(base_path('routes/admin_*.php')) as $adminRoute) {
                Route::middleware('web')->group($adminRoute);
            }

            // Muat seluruh rute Kasir terisolasi
            foreach (glob(base_path('routes/kasir_*.php')) as $kasirRoute) {
                Route::middleware('web')->group($kasirRoute);
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'api/telegram/webhook',
            'telegram/webhook',
        ]);
        $middleware->redirectGuestsTo('/masuk');
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'pending2fa' => \App\Http\Middleware\EnsurePending2fa::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
