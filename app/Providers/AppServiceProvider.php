<?php

namespace App\Providers;

use App\Models\Apbdes;
use App\Models\Complaint;
use App\Models\HeroSlide;
use App\Models\MenuItem;
use App\Models\Official;
use App\Models\Post;
use App\Models\QuickLink;
use App\Models\Statistic;
use App\Models\StatisticDetail;
use App\Observers\CacheClearingObserver;
use App\Observers\ImageConversionObserver;
use App\Policies\ComplaintPolicy;
use App\Settings\GeneralSettings;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Asset;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelSettings\Settings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production for all URLs and assets
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            
            // Force HTTPS for asset URLs (including Filament assets)
            // This ensures all asset() calls return HTTPS URLs
            if (!env('ASSET_URL')) {
                config(['app.asset_url' => config('app.url')]);
            }
            
            // Force secure asset helper globally
            Asset::useSecure();
        }
        
        // Register policies
        Gate::policy(Complaint::class, ComplaintPolicy::class);

        // Prevent CDN optimizers (e.g., Cloudflare Rocket Loader) from mutating Filament / Livewire scripts.
        // Adds data-cfasync="false" to every Filament-managed script and stylesheet tag so Livewire components stay mounted.
        FilamentAsset::resolved(function (AssetManager $assetManager): void {
            // Handle scripts
            foreach ($assetManager->getScripts(withCore: true) as $script) {
                $attributes = $script->getExtraAttributes();

                if (($attributes['data-cfasync'] ?? null) === 'false') {
                    continue;
                }

                $script->extraAttributes([
                    ...$attributes,
                    'data-cfasync' => 'false',
                ]);
            }

            // Handle stylesheets as well
            foreach ($assetManager->getStyles(withCore: true) as $style) {
                $attributes = $style->getExtraAttributes();

                if (($attributes['data-cfasync'] ?? null) === 'false') {
                    continue;
                }

                $style->extraAttributes([
                    ...$attributes,
                    'data-cfasync' => 'false',
                ]);
            }
        });

        // Configure Livewire for better error handling in admin panel
        if (request()->is('admin*')) {
            // Add cache-busting to Livewire scripts
            \Livewire\Livewire::setScriptRoute(function ($handle) {
                return \Filament\Support\Facades\FilamentAsset::getScriptSrc($handle) . '?v=' . time();
            });
        }

        // Global Livewire error handling for admin panel
        \Livewire\Livewire::listen('error', function ($error, $component, $request) {
            if (request()->is('admin*') && str_contains($error->getMessage(), 'Could not find Livewire component')) {
                \Illuminate\Support\Facades\Log::warning('Suppressed Livewire DOM error', [
                    'component' => $component,
                    'error' => $error->getMessage(),
                    'request' => request()->fullUrl(),
                ]);
                // Return a response that doesn't crash the page
                return response()->json(['error' => 'Component temporarily unavailable'], 200);
            }
        });

        // Configure Filament for better modal handling
        if (request()->is('admin*')) {
            // Disable sliding modals which can cause DOM issues
            config(['filament.layout.widgets.modal' => false]);
        }
        
        Settings::register(GeneralSettings::class);

        // Register image conversion observers
        $imageObserver = $this->app->make(ImageConversionObserver::class);
        Post::observe($imageObserver);
        HeroSlide::observe($imageObserver);
        Official::observe($imageObserver);

        // Register cache clearing observers
        $cacheObserver = $this->app->make(CacheClearingObserver::class);
        Post::observe($cacheObserver);
        HeroSlide::observe($cacheObserver);
        MenuItem::observe($cacheObserver);
        Apbdes::observe($cacheObserver);
        Statistic::observe($cacheObserver);
        StatisticDetail::observe($cacheObserver);
        Official::observe($cacheObserver);
        QuickLink::observe($cacheObserver);

        // Register audit logging observer for admin actions
        $auditObserver = $this->app->make(\App\Observers\AuditLogObserver::class);
        Post::observe($auditObserver);
        \App\Models\Agenda::observe($auditObserver);
        Apbdes::observe($auditObserver);
        Official::observe($auditObserver);
        HeroSlide::observe($auditObserver);
        Statistic::observe($auditObserver);
        MenuItem::observe($auditObserver);
        \App\Models\QuickLink::observe($auditObserver);

        // Register Complaint observer for assignment tracking
        Complaint::observe(\App\Observers\ComplaintObserver::class);
    }
}

