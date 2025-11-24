<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;

class PeraturanDesaController
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

        // Dummy data for Peraturan Desa
        $peraturanDesa = [
            [
                'nomor' => '01/PD/2024',
                'tentang' => 'Tata Tertib Pemerintahan Desa Donoharjo',
                'tanggal' => '15 Januari 2024',
                'status' => 'Berlaku',
            ],
            [
                'nomor' => '02/PD/2024',
                'tentang' => 'Peraturan Desa tentang Retribusi Pelayanan',
                'tanggal' => '20 Februari 2024',
                'status' => 'Berlaku',
            ],
            [
                'nomor' => '03/PD/2024',
                'tentang' => 'Peraturan Desa tentang Pengelolaan Sampah',
                'tanggal' => '10 Maret 2024',
                'status' => 'Berlaku',
            ],
            [
                'nomor' => '04/PD/2024',
                'tentang' => 'Peraturan Desa tentang Pemanfaatan Tanah Kas Desa',
                'tanggal' => '25 April 2024',
                'status' => 'Berlaku',
            ],
            [
                'nomor' => '05/PD/2024',
                'tentang' => 'Peraturan Desa tentang Penyelenggaraan Kegiatan Keagamaan',
                'tanggal' => '5 Mei 2024',
                'status' => 'Berlaku',
            ],
        ];

        return view('peraturan-desa', [
            'peraturanDesa' => $peraturanDesa,
            'menuItems' => $menuItems,
            'settings' => $settings,
            'pageTitle' => 'Peraturan Desa - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => 'Peraturan Desa - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaDescription' => 'Daftar peraturan desa yang berlaku di ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'canonicalUrl' => url()->current(),
        ]);
    }
}

