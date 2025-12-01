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
        // Adds data-cfasync="false" to every Filament-managed script tag so Livewire components stay mounted.
        FilamentAsset::resolved(function (AssetManager $assetManager): void {
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
        });
        
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

