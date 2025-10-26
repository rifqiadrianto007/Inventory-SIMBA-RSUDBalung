<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'prevent-back-history' => PreventBackHistory::class,
            'role' => RoleMiddleware::class,
        ]);

        // $middleware->appendToGroup('web', [PreventBackHistory::class]);

        // $middleware->remove(\Illuminate\Cookie\Middleware\EncryptCookies::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
