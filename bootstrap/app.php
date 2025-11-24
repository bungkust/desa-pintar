<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Suppress deprecated warnings for PDO constants in PHP 8.5+
if (PHP_VERSION_ID >= 80500) {
    error_reporting(E_ALL & ~E_DEPRECATED);
}

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\Filament\AdminPanelProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\CacheHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // In production, ensure detailed errors are not exposed
        if (app()->environment('production')) {
            $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
                // Log the full exception details
                \Illuminate\Support\Facades\Log::error('Unhandled exception', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Return generic error page for production
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Server Error',
                        'error' => 'An error occurred. Please try again later.',
                    ], 500);
                }

                // For web requests, Laravel will show generic error page
                return null;
            });
        }
    })->create();

