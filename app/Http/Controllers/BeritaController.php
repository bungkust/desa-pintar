<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Post;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;

class BeritaController
{
    public function index()
    {
        // Get Menu Items for navbar (cached)
        $menuItems = Cache::remember('menu_items', 3600, function () {
            return MenuItem::where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->with(['children' => function ($query) {
                    $query->where('is_active', true)
                          ->orderBy('order')
                          ->with(['children' => function ($subQuery) {
                              $subQuery->where('is_active', true)
                                       ->orderBy('order');
                          }]);
                }])
                ->get();
        });

        // Get settings (cached)
        $settings = Cache::rememberForever('general_settings', function () {
            try {
                return app(GeneralSettings::class);
            } catch (\Exception $e) {
                return (object) [
                    'site_name' => 'Pemerintah Kalurahan Donoharjo',
                    'village_address' => 'Jl. Parasamya, Donoharjo, Ngaglik, Sleman, DIY 55581',
                    'whatsapp' => '6281227666999',
                    'logo_path' => null,
                    'instagram' => null,
                ];
            }
        });

        // Get all published posts (cached for 30 minutes)
        $posts = Cache::remember('all_posts', 1800, function () {
            return Post::published()
                ->orderBy('published_at', 'desc')
                ->paginate(12);
        });

        return view('berita', [
            'posts' => $posts,
            'menuItems' => $menuItems,
            'settings' => $settings,
            'pageTitle' => 'Berita - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => 'Berita - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaDescription' => 'Berita dan informasi terkini dari ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'canonicalUrl' => url()->current(),
        ]);
    }
}

