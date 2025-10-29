<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RoleMiddleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // aktifkan API route
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Grup middleware untuk WEB
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        // Grup middleware untuk API (pakai Sanctum)
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,  // Sanctum
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Alias middleware tambahan
        $middleware->alias([
            'prevent-back-history' => PreventBackHistory::class,
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
