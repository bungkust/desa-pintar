<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateQuickLinkRedirect extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'url' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * Check if URL is safe to redirect to
     */
    public static function isSafeUrl(string $url): bool
    {
        if (empty($url) || $url === '#' || $url === '/#') {
            return false; // Empty or anchor links - show dummy page instead
        }

        // Parse URL
        $parsed = parse_url($url);
        
        if (!$parsed) {
            return false; // Invalid URL
        }

        // If it's a relative path (starts with /), it's safe if it doesn't contain protocol
        if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
            // Validate it's a safe internal path (no path traversal, etc.)
            return !self::containsPathTraversal($url);
        }

        // Must be http:// or https://
        $scheme = $parsed['scheme'] ?? '';
        if (!in_array($scheme, ['http', 'https'])) {
            return false;
        }

        // Get host
        $host = $parsed['host'] ?? '';
        if (empty($host)) {
            return false;
        }

        // Block private/local IP addresses (SSRF protection)
        if (self::isPrivateIp($host)) {
            return false;
        }

        // Block localhost variations
        $hostLower = strtolower($host);
        $localHosts = ['localhost', '127.0.0.1', '0.0.0.0', '::1', '[::1]'];
        if (in_array($hostLower, $localHosts)) {
            return false;
        }

        // Block internal/private hostnames
        if (str_ends_with($hostLower, '.local') || str_ends_with($hostLower, '.localhost')) {
            return false;
        }

        // Whitelist approach: Only allow specific trusted domains
        // TODO: Configure via .env (e.g., ALLOWED_REDIRECT_DOMAINS=domain1.com,domain2.com)
        $allowedDomains = config('app.allowed_redirect_domains', []);
        
        if (!empty($allowedDomains)) {
            $allowedDomains = is_string($allowedDomains) ? explode(',', $allowedDomains) : $allowedDomains;
            $allowedDomains = array_map('trim', $allowedDomains);
            
            $hostMatches = false;
            foreach ($allowedDomains as $domain) {
                if ($hostLower === strtolower($domain) || str_ends_with($hostLower, '.' . strtolower($domain))) {
                    $hostMatches = true;
                    break;
                }
            }
            
            if (!$hostMatches) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if host is a private IP address
     */
    protected static function isPrivateIp(string $host): bool
    {
        // Check if it's an IP address
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            // Check for private IP ranges
            return !filter_var(
                $host,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            );
        }
        
        return false;
    }

    /**
     * Check if path contains path traversal attempts
     */
    protected static function containsPathTraversal(string $path): bool
    {
        // Check for directory traversal patterns
        $dangerous = ['../', '..\\', '..', '%2e%2e', '%2e%2e%2f', '..%2f'];
        
        foreach ($dangerous as $pattern) {
            if (stripos($path, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
