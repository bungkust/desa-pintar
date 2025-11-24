<?php

namespace App\Providers;

use App\Models\Apbdes;
use App\Models\Complaint;
use App\Models\HeroSlide;
use App\Models\MenuItem;
use App\Models\Official;
use App\Models\Post;
use App\Models\Statistic;
use App\Models\StatisticDetail;
use App\Observers\CacheClearingObserver;
use App\Observers\ImageConversionObserver;
use App\Policies\ComplaintPolicy;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Gate;
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
        // Register policies
        Gate::policy(Complaint::class, ComplaintPolicy::class);
        
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
    }
}

