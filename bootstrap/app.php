<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin_barang.php'));

            $kasirRoutes = glob(base_path('routes/kasir_*.php')) ?: [];
            sort($kasirRoutes);

            foreach ($kasirRoutes as $routeFile) {
                Route::middleware('web')
                    ->group($routeFile);
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/masuk');
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'pending2fa' => \App\Http\Middleware\EnsurePending2fa::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
