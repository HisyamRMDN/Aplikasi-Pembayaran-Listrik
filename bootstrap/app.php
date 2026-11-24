<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
        'webhooks/midtrans',
    ]);
    $middleware->alias([
        'ensure.auth' => \App\Http\Middleware\EnsureUserIsAuthenticated::class,
        'admin.only' => \App\Http\Middleware\EnsureAdministrator::class,
    ]);
})

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

