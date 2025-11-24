<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Statistic;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatistikLengkapController
{
    public function show()
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

        // Get all statistics with details, grouped by category (cached)
        $statistics = Cache::remember('statistics_with_details', 3600, function () {
            return Statistic::with(['details' => function ($query) {
            $query->orderBy('year', 'desc');
        }])->orderBy('order')->get()->groupBy(function ($stat) {
            return $stat->category ?? 'lainnya';
            });
        });

        // Prepare data for charts (trend per year for each statistic)
        $chartData = [];
        foreach ($statistics as $category => $categoryStats) {
            foreach ($categoryStats as $stat) {
                $years = $stat->details->pluck('year')->sort()->values()->toArray();
                $values = $stat->details->sortBy('year')->pluck('value')->values()->toArray();
                
                // Extract numeric values for chart
                $numericValues = array_map(function($val) {
                    return (int) preg_replace('/[^0-9]/', '', $val) ?: 0;
                }, $values);

                $chartData[$stat->id] = [
                    'label' => $stat->label,
                    'years' => $years,
                    'values' => $numericValues,
                    'rawValues' => $values,
                ];
            }
        }

        // Get available years for filtering
        $availableYears = DB::table('statistic_details')
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return view('statistik-lengkap', [
            'statistics' => $statistics,
            'chartData' => $chartData,
            'availableYears' => $availableYears,
            'menuItems' => $menuItems,
            'settings' => $settings,
        ]);
    }
}
