<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @php
        use App\Settings\GeneralSettings;
        use Illuminate\Support\Facades\Cache;
        
        // Get settings if not provided
        if (!isset($settings)) {
            $settings = Cache::rememberForever('general_settings', function () {
                try {
                    return app(GeneralSettings::class);
                } catch (\Exception $e) {
                    return (object) [
                        'site_name' => 'Desa Donoharjo',
                        'village_address' => 'Jl. Parasamya, Donoharjo, Ngaglik, Sleman, DIY 55581',
                        'whatsapp' => '6281227666999',
                        'logo_path' => null,
                        'instagram' => null,
                    ];
                }
            });
        }
        
        $siteName = $settings->site_name ?? 'Desa Donoharjo';
        $currentUrl = url()->current();
        $ogImage = isset($ogImage) ? $ogImage : ($settings->logo_path ?? null ? Storage::url($settings->logo_path) : asset('favicon.ico'));
    @endphp
    
    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ $metaTitle ?? $pageTitle ?? $siteName }}">
    <meta name="description" content="{{ $metaDescription ?? $siteName }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#3B82F6">
    
    <!-- Canonical URL - Always present -->
    <link rel="canonical" href="{{ $canonicalUrl ?? $currentUrl }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:title" content="{{ $metaTitle ?? $pageTitle ?? $siteName }}">
    <meta property="og:description" content="{{ $metaDescription ?? $siteName }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="{{ $siteName }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $currentUrl }}">
    <meta name="twitter:title" content="{{ $metaTitle ?? $pageTitle ?? $siteName }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? $siteName }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    
    <title>{{ $pageTitle ?? $siteName }}</title>
    
    <!-- Resource Hints -->
    <link rel="preconnect" href="https://wa.me" crossorigin>
    <link rel="dns-prefetch" href="https://wa.me">
    @if(isset($heroImage))
    <link rel="preload" as="image" href="{{ $heroImage }}" fetchpriority="high">
    @endif
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
    
    <!-- Structured Data -->
    @include('components.structured-data', ['settings' => $settings ?? null, 'post' => $post ?? null])
</head>
<body class="overflow-x-hidden bg-gray-50">
    <!-- Skip to content link for accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-emerald-600 text-white px-4 py-2 rounded-lg z-50">Skip to content</a>

    <!-- Navbar -->
    @include('components.navbar', ['menuItems' => $menuItems ?? collect(), 'settings' => $settings])

    <main id="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.footer', ['settings' => $settings])
    
    @stack('scripts')
</body>
</html>
