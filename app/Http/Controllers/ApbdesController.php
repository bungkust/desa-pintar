<?php

namespace App\Http\Controllers;

use App\Models\Apbdes;
use App\Models\MenuItem;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApbdesController
{
    public function show($year = null)
    {
        // Get available years first (cached)
        $availableYears = Cache::remember('apbdes_available_years', 86400, function () {
            return Apbdes::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        });

        // Validate and set year
        if ($year === null || !is_numeric($year) || !in_array((int)$year, $availableYears)) {
            // Redirect to latest available year if invalid
            if (!empty($availableYears)) {
                return redirect()->route('apbdes.show', ['year' => $availableYears[0]]);
            }
            // No data available - show empty state
            $year = null;
        } else {
            $year = (int)$year;
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

        // Handle empty state
        if ($year === null || empty($availableYears)) {
            return view('apbdes', [
                'year' => null,
                'availableYears' => $availableYears,
                'summary' => null,
                'revenueDetails' => [],
                'expenseDetails' => [],
                'pembiayaanDetails' => [],
                'chartData' => null,
                'menuItems' => $menuItems,
                'settings' => $settings,
                'pageTitle' => 'APBDes - ' . ($settings->site_name ?? 'Desa Donoharjo'),
                'metaTitle' => 'APBDes - ' . ($settings->site_name ?? 'Desa Donoharjo'),
                'metaDescription' => 'Laporan APBDes - Realisasi Anggaran dan Belanja Desa Donoharjo. Transparansi keuangan desa secara lengkap.',
                'canonicalUrl' => url()->current(),
            ]);
        }

        // Get APBDes data for the specified year (cached)
        $apbdesRecords = Cache::remember("apbdes_year_{$year}", 86400, function () use ($year) {
            return Apbdes::where('year', $year)->get();
        });

        // Calculate summary totals
        $summary = [
            'pendapatan' => [
                'realisasi' => 0,
                'anggaran' => 0,
            ],
            'belanja' => [
                'realisasi' => 0,
                'anggaran' => 0,
            ],
            'pembiayaan' => [
                'realisasi' => 0,
                'anggaran' => 0,
            ],
        ];

        // Group by type and calculate totals
        foreach ($apbdesRecords as $record) {
            if (isset($summary[$record->type])) {
                $summary[$record->type]['realisasi'] += $record->realisasi ?? 0;
                $summary[$record->type]['anggaran'] += $record->anggaran ?? 0;
            }
        }

        // Calculate percentages for summary
        foreach ($summary as $type => &$data) {
            $data['percentage'] = $data['anggaran'] > 0 
                ? ($data['realisasi'] / $data['anggaran']) * 100 
                : 0;
        }

        // Group revenue details by category
        $revenueDetails = [];
        $revenueRecords = $apbdesRecords->where('type', 'pendapatan');
        foreach ($revenueRecords as $record) {
            $realisasi = $record->realisasi ?? 0;
            $anggaran = $record->anggaran ?? 0;
            $percentage = $anggaran > 0 ? ($realisasi / $anggaran) * 100 : 0;

            $revenueDetails[] = [
                'category' => $record->category,
                'realisasi' => $realisasi,
                'anggaran' => $anggaran,
                'percentage' => $percentage,
            ];
        }
        // Sort by percentage descending
        usort($revenueDetails, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        // Group expense details by category
        $expenseDetails = [];
        $expenseRecords = $apbdesRecords->where('type', 'belanja');
        foreach ($expenseRecords as $record) {
            $realisasi = $record->realisasi ?? 0;
            $anggaran = $record->anggaran ?? 0;
            $percentage = $anggaran > 0 ? ($realisasi / $anggaran) * 100 : 0;

            $expenseDetails[] = [
                'category' => $record->category,
                'realisasi' => $realisasi,
                'anggaran' => $anggaran,
                'percentage' => $percentage,
            ];
        }
        // Sort by percentage descending
        usort($expenseDetails, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        // Group pembiayaan details by category
        $pembiayaanDetails = [];
        $pembiayaanRecords = $apbdesRecords->where('type', 'pembiayaan');
        foreach ($pembiayaanRecords as $record) {
            $realisasi = $record->realisasi ?? 0;
            $anggaran = $record->anggaran ?? 0;
            $percentage = $anggaran > 0 ? ($realisasi / $anggaran) * 100 : 0;

            $pembiayaanDetails[] = [
                'category' => $record->category,
                'realisasi' => $realisasi,
                'anggaran' => $anggaran,
                'percentage' => $percentage,
            ];
        }
        // Sort by percentage descending
        usort($pembiayaanDetails, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        // Prepare chart data
        $chartData = [
            'pieChartPendapatan' => [
                'labels' => array_column($revenueDetails, 'category'),
                'values' => array_column($revenueDetails, 'realisasi'),
                'colors' => $this->generateColors(count($revenueDetails), 'green'),
            ],
            'pieChartBelanja' => [
                'labels' => array_column($expenseDetails, 'category'),
                'values' => array_column($expenseDetails, 'realisasi'),
                'colors' => $this->generateColors(count($expenseDetails), 'red'),
            ],
            'barChartComparison' => [
                'labels' => ['Pendapatan', 'Belanja', 'Pembiayaan'],
                'realisasi' => [
                    $summary['pendapatan']['realisasi'],
                    $summary['belanja']['realisasi'],
                    $summary['pembiayaan']['realisasi'],
                ],
                'anggaran' => [
                    $summary['pendapatan']['anggaran'],
                    $summary['belanja']['anggaran'],
                    $summary['pembiayaan']['anggaran'],
                ],
            ],
        ];

        return view('apbdes', [
            'year' => $year,
            'availableYears' => $availableYears,
            'summary' => $summary,
            'revenueDetails' => $revenueDetails,
            'expenseDetails' => $expenseDetails,
            'pembiayaanDetails' => $pembiayaanDetails,
            'chartData' => $chartData,
            'menuItems' => $menuItems,
            'settings' => $settings,
            'pageTitle' => 'APBDes ' . $year . ' - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => 'APBDes ' . $year . ' - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaDescription' => 'Laporan APBDes ' . $year . ' - Realisasi Anggaran dan Belanja Desa Donoharjo. Transparansi keuangan desa secara lengkap.',
            'canonicalUrl' => url()->current(),
        ]);
    }

    /**
     * Generate color array for charts based on type
     */
    private function generateColors($count, $baseType = 'blue')
    {
        $colorSchemes = [
            'green' => ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#d1fae5'],
            'red' => ['#ef4444', '#f87171', '#fca5a5', '#fecaca', '#fee2e2'],
            'blue' => ['#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe', '#dbeafe'],
            'orange' => ['#f97316', '#fb923c', '#fdba74', '#fed7aa', '#ffedd5'],
        ];

        $scheme = $colorSchemes[$baseType] ?? $colorSchemes['blue'];
        $colors = [];
        
        for ($i = 0; $i < $count; $i++) {
            $colors[] = $scheme[$i % count($scheme)];
        }

        return $colors;
    }
}
