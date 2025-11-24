<?php

namespace App\Providers;

use App\Models\Apbdes;
use App\Models\HeroSlide;
use App\Models\MenuItem;
use App\Models\Official;
use App\Models\Post;
use App\Models\Statistic;
use App\Models\StatisticDetail;
use App\Observers\CacheClearingObserver;
use App\Observers\ImageConversionObserver;
use App\Settings\GeneralSettings;
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
    }
}

