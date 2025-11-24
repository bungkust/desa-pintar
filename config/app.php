<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    */
    'name' => env('APP_NAME', 'Desa Donoharjo'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    */
    'env' => env('APP_ENV', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    */
    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    */
    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    */
    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    */
    'locale' => env('APP_LOCALE', 'id'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'id'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'id_ID'),

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Security Configuration
    |--------------------------------------------------------------------------
    |
    | Restrict admin panel access by email domain in production.
    | Leave empty to allow all verified emails.
    | Example: 'example.com' will only allow emails ending with @example.com
    |
    */
    'admin_email_domain' => env('APP_ADMIN_EMAIL_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | Allowed Redirect Domains
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of allowed domains for external redirects.
    | This prevents SSRF (Server-Side Request Forgery) attacks.
    | Leave empty to allow all external domains (not recommended for production).
    | Example: 'google.com,youtube.com,example.com'
    |
    */
    'allowed_redirect_domains' => env('ALLOWED_REDIRECT_DOMAINS', '') 
        ? array_map('trim', explode(',', env('ALLOWED_REDIRECT_DOMAINS', ''))) 
        : [],

];

