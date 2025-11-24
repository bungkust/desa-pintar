<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ApbdesController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\LayananSuratController;
use App\Http\Controllers\PeraturanDesaController;
use App\Models\Apbdes;
use App\Models\HeroSlide;
use App\Models\MenuItem;
use App\Models\Official;
use App\Models\Post;
use App\Models\QuickLink;
use App\Models\Statistic;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelSettings\Exceptions\MissingSettings;

Route::get('/', function () {
    // Get first active HeroSlide (cached for 1 hour)
    $heroSlide = Cache::remember('hero_slide_active', 3600, function () {
        return HeroSlide::where('is_active', true)
        ->orderBy('order')
        ->first();
    });

    // Get Menu Items for navbar (cached for 1 hour)
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

    // Get QuickLinks (cached for 1 hour)
    $quickLinks = Cache::remember('quick_links', 3600, function () {
        return QuickLink::orderBy('order')->limit(8)->get();
    });

    // Get Statistics grouped by category (cached for 1 hour)
    $statistics = Cache::remember('statistics', 3600, function () {
        return Statistic::orderBy('order')->get()->groupBy(function ($stat) {
        return $stat->category ?? 'lainnya';
        });
    });

    // Get latest published Posts (cached for 30 minutes)
    $posts = Cache::remember('latest_posts', 1800, function () {
        return Post::published()
        ->orderBy('published_at', 'desc')
        ->limit(3)
        ->get();
    });

    // Get Lurah Official (cached for 1 hour)
    $lurah = Cache::remember('lurah_official', 3600, function () {
        return Official::where('position', 'Lurah')->first();
    });

    // Get latest year Apbdes data for summary (cached for 24 hours)
    $apbdesData = Cache::remember('apbdes_summary', 86400, function () {
    $latestYear = Apbdes::max('year');
        if (!$latestYear) {
            return null;
        }

        $pendapatanRealisasi = Apbdes::where('year', $latestYear)
            ->where('type', 'pendapatan')
            ->sum('realisasi');
        $pendapatanAnggaran = Apbdes::where('year', $latestYear)
            ->where('type', 'pendapatan')
            ->sum('anggaran');
        
        $belanjaRealisasi = Apbdes::where('year', $latestYear)
            ->where('type', 'belanja')
            ->sum('realisasi');
        $belanjaAnggaran = Apbdes::where('year', $latestYear)
            ->where('type', 'belanja')
            ->sum('anggaran');
        
        $pembiayaanRealisasi = Apbdes::where('year', $latestYear)
            ->where('type', 'pembiayaan')
            ->sum('realisasi');
        $pembiayaanAnggaran = Apbdes::where('year', $latestYear)
            ->where('type', 'pembiayaan')
            ->sum('anggaran');

        return [
            'year' => $latestYear,
            'pendapatan' => [
                'realisasi' => $pendapatanRealisasi,
                'anggaran' => $pendapatanAnggaran,
                'percentage' => $pendapatanAnggaran > 0 ? ($pendapatanRealisasi / $pendapatanAnggaran) * 100 : 0,
            ],
            'belanja' => [
                'realisasi' => $belanjaRealisasi,
                'anggaran' => $belanjaAnggaran,
                'percentage' => $belanjaAnggaran > 0 ? ($belanjaRealisasi / $belanjaAnggaran) * 100 : 0,
            ],
            'pembiayaan' => [
                'realisasi' => $pembiayaanRealisasi,
                'anggaran' => $pembiayaanAnggaran,
                'percentage' => $pembiayaanAnggaran > 0 ? ($pembiayaanRealisasi / $pembiayaanAnggaran) * 100 : 0,
            ],
        ];
    });

    // Get GeneralSettings (cached forever, cleared on settings update)
    $settings = Cache::rememberForever('general_settings', function () {
    $defaults = [
        'site_name' => 'Pemerintah Kalurahan Donoharjo',
        'village_address' => 'Jl. Parasamya, Donoharjo, Ngaglik, Sleman, DIY 55581',
        'whatsapp' => '6281227666999',
        'logo_path' => null,
        'instagram' => null,
    ];
        
        $settingsCount = DB::table('settings')->where('group', 'general')->count();
    
    if ($settingsCount < 5) {
        foreach ($defaults as $name => $value) {
            DB::table('settings')->insertOrIgnore([
                'group' => 'general',
                'name' => $name,
                'payload' => json_encode($value),
                'locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    try {
            return app(GeneralSettings::class);
    } catch (\Exception $e) {
            return (object) $defaults;
    }
    });

    return view('welcome', [
        'heroSlide' => $heroSlide,
        'menuItems' => $menuItems,
        'quickLinks' => $quickLinks,
        'statistics' => $statistics,
        'posts' => $posts,
        'lurah' => $lurah,
        'apbdesData' => $apbdesData,
        'settings' => $settings,
    ]);
});

// Route for individual post pages
Route::get('/posts/{slug}', function ($slug) {
    $post = Cache::remember("post_{$slug}", 1800, function () use ($slug) {
        return Post::where('slug', $slug)->whereNotNull('published_at')->firstOrFail();
    });
    
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
    
    return view('post', [
        'post' => $post,
        'menuItems' => $menuItems,
        'settings' => $settings,
    ]);
})->name('post.show');

// Route for APBDes detail page
Route::get('/apbdes/{year?}', [ApbdesController::class, 'show'])->name('apbdes.show');

// Route for Statistik Lengkap page
Route::get('/statistik-lengkap', [\App\Http\Controllers\StatistikLengkapController::class, 'show'])->name('statistik-lengkap');

// Route for Berita page
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');

// Route for Peraturan Desa page
Route::get('/peraturan-desa', [PeraturanDesaController::class, 'index'])->name('peraturan-desa');

// Route for Layanan Surat page
Route::get('/layanan-surat', [LayananSuratController::class, 'index'])->name('layanan-surat');

// Routes for Agenda
Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
Route::get('/agenda/{id}', [AgendaController::class, 'show'])->name('agenda.show');

// Routes for Pages
Route::get('/pajak-pbb', function () {
    return app(\App\Http\Controllers\PageController::class)->show('pajak-pbb');
})->name('pages.pajak-pbb');

Route::get('/peladi-makarti', function () {
    return app(\App\Http\Controllers\PageController::class)->show('peladi-makarti');
})->name('pages.peladi-makarti');

Route::get('/survey-ikm', function () {
    return app(\App\Http\Controllers\PageController::class)->show('survey-ikm');
})->name('pages.survey-ikm');

Route::get('/survey-korupsi', function () {
    return app(\App\Http\Controllers\PageController::class)->show('survey-korupsi');
})->name('pages.survey-korupsi');

// Route for sitemap.xml
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Routes for Quick Links dummy pages
Route::get('/potensi-desa', function () {
    $controller = new \App\Http\Controllers\QuickLinkController();
    return $controller->showDummyPage('Potensi Desa');
})->name('potensi-desa');

Route::get('/pengaduan', function () {
    $controller = new \App\Http\Controllers\QuickLinkController();
    return $controller->showDummyPage('Pengaduan');
})->name('pengaduan');

// Generic quick link route (for dynamic labels)
Route::get('/quick-link/{label}', [\App\Http\Controllers\QuickLinkController::class, 'redirect'])->name('quick-link.show');

