<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Content Security Policy
        // Note: 'unsafe-inline' and 'unsafe-eval' are required for Filament/Livewire to work
        // In production, consider using nonces or hashes for better security
        
        // Allow Vite dev server in development mode
        $isDev = app()->environment('local', 'development');
        
        $viteHttp = $isDev ? 'http://127.0.0.1:5173 http://localhost:5173' : '';
        $viteWs = $isDev ? 'ws://127.0.0.1:5173 ws://localhost:5173' : '';
        
        $scriptSrc = "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net" . ($viteHttp ? ' ' . $viteHttp : '');
        $styleSrc = "'self' 'unsafe-inline' https://fonts.bunny.net" . ($viteHttp ? ' ' . $viteHttp : '');
        $connectSrc = "'self' https://wa.me https://fonts.bunny.net https://cdn.jsdelivr.net" . ($viteHttp ? ' ' . $viteHttp : '') . ($viteWs ? ' ' . $viteWs : '');
        
        $csp = "default-src 'self'; " .
            "script-src {$scriptSrc}; " .
            "style-src {$styleSrc}; " .
            "img-src 'self' data: https: blob:; " .
            "font-src 'self' data: https://fonts.bunny.net; " .
            "connect-src {$connectSrc}; " .
            "frame-src 'self'; " .
            "frame-ancestors 'self'; " .
            "object-src 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self';" . 
            ($isDev ? '' : ' upgrade-insecure-requests;');
        
        $response->headers->set('Content-Security-Policy', $csp);
        
        // X-Content-Type-Options
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // X-Frame-Options
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Referrer-Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions-Policy (formerly Feature-Policy)
        $response->headers->set('Permissions-Policy', 
            'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=()'
        );
        
        // X-XSS-Protection (legacy but still useful for older browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Strict-Transport-Security (HSTS) - only set if HTTPS
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
        
        // Expect-CT (Certificate Transparency)
        // Note: This header is deprecated but still used by some browsers
        // Consider removing in the future
        
        return $response;
    }
}

