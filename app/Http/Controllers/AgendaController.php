<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateAgendaSearch;
use App\Models\Agenda;
use App\Models\MenuItem;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AgendaController
{
    public function index(ValidateAgendaSearch $request)
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

        // Build query
        $query = Agenda::upcoming();

        // Filter by category (validated)
        $validated = $request->validated();
        
        if (!empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }

        // Filter by date (validated)
        if (!empty($validated['date'])) {
            $query->whereDate('date', $validated['date']);
        }

        // Search by title (validated and sanitized)
        $search = $request->getValidatedSearch();
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        // Get view mode (table or card), default to card (validated)
        $viewMode = $validated['view'] ?? 'card';
        
        // Get agendas (not grouped, just a simple collection)
        // Note: scopeUpcoming already orders by date and start_time
        // Use a new cache key to avoid conflicts with old grouped data
        $cacheKey = 'agendas_list_v2_' . md5($request->getQueryString());
        $agendas = Cache::remember($cacheKey, 900, function () use ($query) {
            // Make sure we get a flat collection, not grouped
            return $query->get();
        });

        // Get all categories for filter
        $categories = [
            'pemerintahan' => 'Pemerintahan',
            'kesehatan' => 'Kesehatan',
            'lingkungan' => 'Lingkungan',
            'budaya' => 'Budaya',
            'umum' => 'Umum',
        ];

        return view('agenda.index', [
            'agendas' => $agendas,
            'menuItems' => $menuItems,
            'settings' => $settings,
            'categories' => $categories,
            'viewMode' => $viewMode,
            'pageTitle' => 'Agenda Desa - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => 'Agenda Desa - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaDescription' => 'Agenda kegiatan dan acara desa ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'canonicalUrl' => url()->current(),
        ]);
    }

    public function show($id)
    {
        // Validate ID is a positive integer
        $id = filter_var($id, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);
        
        if ($id === false) {
            abort(404);
        }

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

        // Get agenda (cached for 30 minutes)
        $agenda = Cache::remember("agenda_{$id}", 1800, function () use ($id) {
            return Agenda::findOrFail($id);
        });

        return view('agenda.show', [
            'agenda' => $agenda,
            'menuItems' => $menuItems,
            'settings' => $settings,
            'pageTitle' => $agenda->title . ' - Agenda Desa - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => $agenda->title . ' - Agenda Desa',
            'metaDescription' => Str::limit(strip_tags($agenda->description ?? $agenda->title), 160),
            'canonicalUrl' => url()->current(),
        ]);
    }
}

