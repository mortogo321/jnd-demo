<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.session_first' => \App\Http\Middleware\AuthenticateWithSessionFirst::class,
        ]);

        // Exclude XSRF-TOKEN from cookie encryption (needed for SPA CSRF protection)
        $middleware->encryptCookies(except: [
            'XSRF-TOKEN',
        ]);

        // Enable Sanctum's stateful API middleware for cookie authentication
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
