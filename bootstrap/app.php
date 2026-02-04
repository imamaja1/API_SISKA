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
        then: function (): void {
            Route::middleware('api')->group(base_path('routes/api-simortua.php'));
            Route::middleware('api')->group(base_path('routes/api-obe.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Enable Sanctum SPA cookie auth for stateful domains (session + CSRF on API routes).
        $middleware->statefulApi();

        $middleware->alias([
            'dosen' => \App\Http\Middleware\EnsureDosenToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
