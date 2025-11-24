<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function show(string $slug)
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

        // Validate slug format (alphanumeric, hyphens only)
        if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
            abort(404);
        }

        // Map slug to view name (whitelist approach for security)
        $views = [
            'pajak-pbb' => 'pages.pajak-pbb',
            'peladi-makarti' => 'pages.peladi-makarti',
            'survey-ikm' => 'pages.survey-ikm',
            'survey-korupsi' => 'pages.survey-korupsi',
        ];

        if (!isset($views[$slug])) {
            abort(404);
        }

        $view = $views[$slug];
        
        return view($view, [
            'menuItems' => $menuItems,
            'settings' => $settings,
            'pageTitle' => ucfirst(str_replace('-', ' ', $slug)) . ' - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => ucfirst(str_replace('-', ' ', $slug)) . ' - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaDescription' => 'Informasi tentang ' . str_replace('-', ' ', $slug) . ' di ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'canonicalUrl' => url()->current(),
        ]);
    }
}