<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\QuickLink;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class QuickLinkController
{
    /**
     * Handle quick link redirection based on label slug
     */
    public function redirect($label)
    {
        // Normalize label (convert slug back to label format)
        $normalizedLabel = ucwords(str_replace(['-', '_'], ' ', $label));
        
        // Try to find quick link by label
        $quickLink = QuickLink::where('label', $normalizedLabel)
            ->orWhere('label', 'like', '%' . $label . '%')
            ->first();

        if ($quickLink) {
            return $this->handleQuickLinkRedirect($quickLink);
        }

        // If not found, show dummy page with the label
        return $this->showDummyPage($normalizedLabel);
    }

    /**
     * Handle quick link based on the URL field
     */
    protected function handleQuickLinkRedirect(QuickLink $quickLink)
    {
        $url = $quickLink->url;

        // If URL starts with http:// or https://, redirect directly
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return redirect($url);
        }

        // If URL is empty or '#', show dummy page
        if (empty($url) || $url === '#' || $url === '/#') {
            return $this->showDummyPage($quickLink->label);
        }

        // Try to resolve as route name first
        if (Route::has($url)) {
            return redirect()->route($url);
        }

        // Try as absolute path
        if (str_starts_with($url, '/')) {
            // Check if route exists for this path
            try {
                return redirect($url);
            } catch (\Exception $e) {
                return $this->showDummyPage($quickLink->label);
            }
        }

        // Fallback to dummy page
        return $this->showDummyPage($quickLink->label);
    }

    /**
     * Handle common routes based on label or URL pattern
     */
    protected function handleCommonRoutes($slug)
    {
        $routeMap = [
            'layanan-surat' => 'layanan-surat',
            'layanan' => 'layanan-surat',
            'peraturan' => 'peraturan-desa',
            'peraturan-desa' => 'peraturan-desa',
            'produk-hukum' => 'peraturan-desa',
            'berita' => 'berita',
            'apbdes' => 'apbdes.show',
            'statistik' => 'statistik-lengkap',
            'potensi' => 'potensi-desa',
            'potensi-desa' => 'potensi-desa',
            'pengaduan' => 'pengaduan',
        ];

        $normalizedSlug = strtolower(str_replace([' ', '-', '_'], '-', $slug));

        if (isset($routeMap[$normalizedSlug])) {
            $route = $routeMap[$normalizedSlug];
            
            if ($route === 'apbdes.show') {
                // Get latest year for APBDes
                $latestYear = \App\Models\Apbdes::max('year');
                if ($latestYear) {
                    return redirect()->route($route, ['year' => $latestYear]);
                }
            }
            
            if (Route::has($route)) {
                return redirect()->route($route);
            }
        }

        // Default to homepage if nothing matches
        return redirect('/');
    }

    /**
     * Show dummy page for quick links that don't have pages yet
     */
    public function showDummyPage($title = null)
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

        // Generate dummy content based on title
        $content = $this->generateDummyContent($title);

        return view('quick-link-dummy', [
            'title' => $title,
            'content' => $content,
            'menuItems' => $menuItems,
            'settings' => $settings,
            'pageTitle' => $title . ' - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaTitle' => $title . ' - ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'metaDescription' => 'Informasi tentang ' . strtolower($title) . ' dari ' . ($settings->site_name ?? 'Desa Donoharjo'),
            'canonicalUrl' => url()->current(),
        ]);
    }

    /**
     * Generate dummy content based on title
     */
    protected function generateDummyContent($title)
    {
        $contentMap = [
            'Layanan Surat' => [
                'title' => 'Layanan Surat',
                'description' => 'Halaman ini akan segera tersedia. Informasi lengkap tentang layanan surat akan ditampilkan di sini.',
                'items' => [
                    'Surat Keterangan Domisili',
                    'Surat Keterangan Tidak Mampu (SKTM)',
                    'Surat Keterangan Usaha',
                    'Surat Pengantar KTP',
                ],
            ],
            'Produk Hukum' => [
                'title' => 'Produk Hukum',
                'description' => 'Halaman ini akan segera tersedia. Informasi tentang peraturan dan produk hukum desa akan ditampilkan di sini.',
                'items' => [
                    'Peraturan Desa',
                    'Keputusan Kepala Desa',
                    'Surat Edaran',
                ],
            ],
            'Potensi Desa' => [
                'title' => 'Potensi Desa',
                'description' => 'Halaman ini akan segera tersedia. Informasi tentang potensi dan daya tarik desa akan ditampilkan di sini.',
                'items' => [
                    'Wisata',
                    'UMKM',
                    'Sumber Daya Alam',
                    'Budaya dan Adat',
                ],
            ],
            'Pengaduan' => [
                'title' => 'Pengaduan Masyarakat',
                'description' => 'Halaman ini akan segera tersedia. Formulir pengaduan masyarakat akan tersedia di sini.',
                'items' => [
                    'Formulir Pengaduan Online',
                    'Status Pengaduan',
                    'Riwayat Pengaduan',
                ],
            ],
        ];

        return $contentMap[$title] ?? [
            'title' => $title,
            'description' => 'Halaman ini sedang dalam pengembangan dan akan segera tersedia.',
            'items' => [],
        ];
    }
}

