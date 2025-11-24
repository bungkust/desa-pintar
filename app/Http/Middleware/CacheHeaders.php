<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        $path = $request->path();
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        // Static assets (JS, CSS, images, fonts)
        if (in_array($extension, ['js', 'css', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'woff', 'woff2', 'ttf', 'eot', 'ico'])) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        }
        // Images in storage
        elseif (str_starts_with($path, 'storage/')) {
            $response->headers->set('Cache-Control', 'public, max-age=2592000');
        }
        // HTML pages
        else {
            $response->headers->set('Cache-Control', 'public, max-age=3600, must-revalidate');
            $response->headers->set('ETag', md5($response->getContent()));
        }
        
        // Vary header for compressed content
        $response->headers->set('Vary', 'Accept-Encoding');
        
        return $response;
    }
}

