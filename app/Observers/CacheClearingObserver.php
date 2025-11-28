<?php

namespace App\Observers;

use App\Models\Apbdes;
use App\Models\HeroSlide;
use App\Models\MenuItem;
use App\Models\Official;
use App\Models\Post;
use App\Models\QuickLink;
use App\Models\Statistic;
use App\Models\StatisticDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CacheClearingObserver
{
    /**
     * Handle the model "saved" event.
     * Clear relevant caches when models are saved.
     */
    public function saved(Model $model): void
    {
        $this->clearCaches($model);
    }

    /**
     * Handle the model "deleted" event.
     * Clear relevant caches when models are deleted.
     */
    public function deleted(Model $model): void
    {
        $this->clearCaches($model);
    }

    /**
     * Clear relevant caches based on model type
     */
    protected function clearCaches(Model $model): void
    {
        $modelClass = get_class($model);

        match ($modelClass) {
            Post::class => $this->clearPostCaches(),
            HeroSlide::class => $this->clearHeroSlideCaches(),
            MenuItem::class => $this->clearMenuItemCaches($model),
            Apbdes::class => $this->clearApbdesCaches($model),
            Statistic::class => $this->clearStatisticCaches(),
            StatisticDetail::class => $this->clearStatisticDetailCaches(),
            Official::class => $this->clearOfficialCaches($model),
            QuickLink::class => $this->clearQuickLinkCaches(),
            default => null,
        };

        // Always clear general settings cache if it exists
        Cache::forget('general_settings');
    }

    protected function clearPostCaches(): void
    {
        Cache::forget('posts');
        Cache::forget('posts_latest');
    }

    protected function clearHeroSlideCaches(): void
    {
        Cache::forget('hero_slide');
        Cache::forget('hero_slides');
    }

    protected function clearMenuItemCaches(MenuItem $model): void
    {
        Cache::forget('menu_items');
        // Clear parent options cache (will regenerate on next form load)
        // Using a tag-like approach: clear by pattern if using Redis
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getRedis()->connection();
                $keys = $redis->keys('*menu_parent_options*');
                foreach ($keys as $key) {
                    $cleanKey = str_replace([config('cache.prefix') . ':', config('database.redis.options.prefix', '')], '', $key);
                    Cache::forget($cleanKey);
                }
            }
        } catch (\Exception $e) {
            // If Redis pattern matching fails, just clear known keys
            Cache::forget('menu_parent_options_' . $model->id);
            Cache::forget('menu_parent_options_new');
        }
    }

    protected function clearApbdesCaches(Apbdes $model): void
    {
        Cache::forget('apbdes_available_years');
        Cache::forget('apbdes_records_' . $model->year);
        Cache::forget('apbdes_filter_years');
        Cache::forget('apbdes_filter_categories');
        Cache::forget('apbdes_data');
    }

    protected function clearStatisticCaches(): void
    {
        Cache::forget('statistics');
        Cache::forget('statistics_all');
    }

    protected function clearStatisticDetailCaches(): void
    {
        Cache::forget('statistics_all');
        Cache::forget('statistics');
    }

    protected function clearOfficialCaches(Official $model): void
    {
        // Always clear lurah cache on any Official update
        // This ensures cache is cleared even if position changes
        Cache::forget('lurah_official');
        
        // Also clear if this is or was the Lurah
        $isLurah = $model->position === 'Lurah';
        $wasLurah = $model->wasChanged('position') && $model->getOriginal('position') === 'Lurah';
        
        if ($isLurah || $wasLurah) {
            // Clear multiple times to ensure it's gone
            Cache::forget('lurah_official');
            Cache::forget('hero_slide_active'); // In case lurah photo is used elsewhere
        }
    }

    protected function clearQuickLinkCaches(): void
    {
        Cache::forget('quick_links');
    }
}
