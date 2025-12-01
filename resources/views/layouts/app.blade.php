<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    {{-- Prevent Cloudflare Rocket Loader from interfering with Livewire/Alpine.js on admin pages --}}
    @if(request()->is('admin*'))
        <meta name="robots" content="noindex, nofollow">
        <script data-cfasync="false">
            // Prevent Cloudflare Rocket Loader interference and ensure Livewire stability
            window.Livewire = window.Livewire || {};

            // Global Alpine.js error handler for Livewire components
            document.addEventListener('alpine:init', () => {
                // Override Alpine's error handler to suppress DOM-related errors
                const originalHandler = Alpine.onError;
                Alpine.onError = (error, el, expression) => {
                    // Suppress "Could not find Livewire component in DOM tree" errors
                    if (error.message && error.message.includes('Could not find Livewire component in DOM tree')) {
                        console.warn('Suppressed Livewire DOM error:', error.message);
                        return;
                    }
                    // Call original handler for other errors
                    if (originalHandler) {
                        originalHandler(error, el, expression);
                    }
                };

                // Add magic method to safely access Livewire components
                Alpine.magic('wire', () => {
                    return window.Livewire?.find || (() => null);
                });

                // Add safe access to wire properties
                Alpine.magic('wireProperty', (el, expression) => {
                    return (property) => {
                        try {
                            const component = window.Livewire?.find?.(el.closest('[wire\\:id]')?.getAttribute('wire:id'));
                            return component ? component[property] : null;
                        } catch (e) {
                            return null;
                        }
                    };
                });
            });

            // Prevent modal-related errors by ensuring proper cleanup
            document.addEventListener('DOMContentLoaded', () => {
                // Override dispatchEvent to handle modal events safely
                const originalDispatch = EventTarget.prototype.dispatchEvent;
                EventTarget.prototype.dispatchEvent = function(event) {
                    try {
                        return originalDispatch.call(this, event);
                    } catch (error) {
                        if (error.message && error.message.includes('Could not find Livewire component')) {
                            console.warn('Suppressed modal dispatch error:', error.message);
                            return false;
                        }
                        throw error;
                    }
                };

                // Add specific handler for Filament modal events
                document.addEventListener('modal-closed', (event) => {
                    // Give time for DOM to settle before cleaning up
                    setTimeout(() => {
                        // Clean up any orphaned modal elements
                        const orphanedModals = document.querySelectorAll('[wire\\:key*="table-action"], [wire\\:key*="table-bulk-action"]');
                        orphanedModals.forEach(modal => {
                            if (!modal.closest('[wire\\:id]')) {
                                console.log('Removing orphaned modal element');
                                modal.remove();
                            }
                        });
                    }, 100);
                });

                // Override Filament's modal close behavior to prevent Alpine errors
                const originalCloseModal = window.closeModal || (() => {});
                window.closeModal = function(id) {
                    try {
                        // Check if the modal element still exists and has a Livewire component
                        const modal = document.querySelector(`[wire\\:key*="${id}"]`);
                        if (modal && modal.closest('[wire\\:id]')) {
                            return originalCloseModal(id);
                        } else {
                            // Modal is orphaned, remove it directly
                            if (modal) modal.remove();
                            return true;
                        }
                    } catch (error) {
                        console.warn('Error closing modal:', error);
                        // Force remove the modal if it exists
                        const modal = document.querySelector(`[wire\\:key*="${id}"]`);
                        if (modal) modal.remove();
                        return true;
                    }
                };

                // Add a mutation observer to handle DOM changes that might affect Livewire components
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'childList') {
                            // Check if any removed nodes contain Livewire components
                            mutation.removedNodes.forEach((node) => {
                                if (node.nodeType === Node.ELEMENT_NODE && node.hasAttribute('wire:id')) {
                                    console.log('Livewire component removed from DOM:', node.getAttribute('wire:id'));
                                }
                            });
                        }
                    });
                });

                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            });
        </script>
    @endif
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
