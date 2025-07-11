<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole; 

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class  // <--- DI SINI MASALAHNYA!
            // Harusnya ada koma setelah CheckRole::class
        ]);

        $middleware->validateCsrfTokens(except: [
        'api/midtrans-notification', // Or 'midtrans-notification' if no /api prefix
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
