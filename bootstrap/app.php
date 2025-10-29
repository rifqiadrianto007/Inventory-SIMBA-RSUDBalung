<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        // api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware khusus untuk web
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            PreventBackHistory::class,
        ]);

        // Alias middleware agar bisa dipanggil langsung di route
        $middleware->alias([
            'prevent-back-history' => PreventBackHistory::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Contoh: jika mau menghapus middleware default, bisa aktifkan ini
        // $middleware->remove(\Illuminate\Cookie\Middleware\EncryptCookies::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
