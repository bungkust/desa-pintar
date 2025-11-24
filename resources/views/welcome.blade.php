@extends('layouts.app')

@php
    $pageTitle = ($settings->site_name ?? 'Desa Donoharjo') . ' - Beranda';
    $metaTitle = $settings->site_name ?? 'Desa Donoharjo';
    $metaDescription = 'Website resmi ' . ($settings->site_name ?? 'Desa Donoharjo') . '. Informasi desa, statistik, berita, dan transparansi APBDes.';
    $heroImage = ($heroSlide && $heroSlide->image) ? (str_starts_with($heroSlide->image, 'http://') || str_starts_with($heroSlide->image, 'https://') ? $heroSlide->image : Storage::url($heroSlide->image)) : null;
@endphp
@push('styles')
    @if($heroImage)
    <link rel="preload" as="image" href="{{ $heroImage }}" fetchpriority="high">
    @endif
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="relative h-[90vh] min-h-[600px] flex items-center justify-center text-white overflow-hidden hero-section" 
             @if($heroSlide && $heroSlide->image)
                 style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.5)), url('{{ str_starts_with($heroSlide->image, 'http://') || str_starts_with($heroSlide->image, 'https://') ? $heroSlide->image : Storage::url($heroSlide->image) }}'); background-size: cover; background-position: center;"
             @else
                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
             @endif>
        <!-- Overlay Gradient -->
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/30"></div>
        
        <!-- Content -->
        <div class="container mx-auto px-4 md:px-6 lg:px-8 py-20 md:py-24 lg:py-32 text-center relative z-10">
            <div class="max-w-4xl mx-auto">
                @if($heroSlide)
                    <h1 class="text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold mb-4 md:mb-6 leading-tight drop-shadow-lg animate-fade-in">
                        {{ $heroSlide->title }}
                    </h1>
                    <p class="text-base md:text-lg lg:text-xl xl:text-2xl max-w-3xl mx-auto leading-relaxed drop-shadow-md animate-fade-in-delay">
                        {{ $heroSlide->subtitle }}
                    </p>
                @else
                    <h1 class="text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold mb-4 md:mb-6 leading-tight drop-shadow-lg">
                        Selamat Datang di {{ $settings->site_name ?? 'Desa Donoharjo' }}
                    </h1>
                    <p class="text-base md:text-lg lg:text-xl xl:text-2xl max-w-3xl mx-auto leading-relaxed drop-shadow-md">
                        Website resmi desa kami
                    </p>
                @endif
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    <!-- Floating QuickLinks Cards -->
    @if($quickLinks->count() > 0)
    <section class="container mx-auto px-4 md:px-6 lg:px-8 -mt-16 md:-mt-20 lg:-mt-24 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-4 lg:gap-6">
            @foreach($quickLinks as $link)
            @php
                // Helper function to resolve quick link URL
                $resolveQuickLinkUrl = function($link) {
                    $url = trim($link->url ?? '');
                    $openInNewTab = false;
                    
                    // Map quick link labels to routes (for backward compatibility)
                    $linkRouteMap = [
                        'Layanan Surat' => 'layanan-surat',
                        'Produk Hukum' => 'peraturan-desa',
                        'Potensi Desa' => 'potensi-desa',
                        'Pengaduan' => 'complaints.index',
                        'Berita' => 'berita',
                        'APBDes' => 'apbdes.show',
                        'Statistik' => 'statistik-lengkap',
                    ];
                    
                    // If URL is empty or #, try to map from label
                    if (empty($url) || $url === '#' || $url === '/#') {
                        $routeName = $linkRouteMap[$link->label] ?? null;
                        if ($routeName && \Illuminate\Support\Facades\Route::has($routeName)) {
                            // Special handling for apbdes.show which needs year parameter
                            if ($routeName === 'apbdes.show') {
                                $latestYear = \App\Models\Apbdes::max('year');
                                if ($latestYear) {
                                    return [route($routeName, ['year' => $latestYear]), false];
                                }
                            }
                            return [route($routeName), false];
                        }
                        // Fallback to generic quick link route
                        return [route('quick-link.show', ['label' => strtolower(str_replace(' ', '-', $link->label))]), false];
                    }
                    
                    // External URL
                    if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                        return [$url, true];
                    }
                    
                    // Internal path starting with /
                    if (str_starts_with($url, '/')) {
                        return [$url, false];
                    }
                    
                    // Try as route name
                    if (\Illuminate\Support\Facades\Route::has($url)) {
                        try {
                            // Special handling for apbdes.show which needs year parameter
                            if ($url === 'apbdes.show') {
                                $latestYear = \App\Models\Apbdes::max('year');
                                if ($latestYear) {
                                    return [route($url, ['year' => $latestYear]), false];
                                }
                            }
                            return [route($url), false];
                        } catch (\Exception $e) {
                            // Route exists but needs parameters - use dummy page
                            $routeName = $linkRouteMap[$link->label] ?? null;
                            if ($routeName && \Illuminate\Support\Facades\Route::has($routeName)) {
                                if ($routeName === 'apbdes.show') {
                                    $latestYear = \App\Models\Apbdes::max('year');
                                    if ($latestYear) {
                                        return [route($routeName, ['year' => $latestYear]), false];
                                    }
                                }
                                return [route($routeName), false];
                            }
                            return [route('quick-link.show', ['label' => strtolower(str_replace(' ', '-', $link->label))]), false];
                        }
                    }
                    
                    // Unknown format - try to map from label or use dummy page
                    $routeName = $linkRouteMap[$link->label] ?? null;
                    if ($routeName && \Illuminate\Support\Facades\Route::has($routeName)) {
                        if ($routeName === 'apbdes.show') {
                            $latestYear = \App\Models\Apbdes::max('year');
                            if ($latestYear) {
                                return [route($routeName, ['year' => $latestYear]), false];
                            }
                        }
                        return [route($routeName), false];
                    }
                    return [route('quick-link.show', ['label' => strtolower(str_replace(' ', '-', $link->label))]), false];
                };
                
                [$actualUrl, $openInNewTab] = $resolveQuickLinkUrl($link);
            @endphp
            <a href="{{ $actualUrl }}" @if($openInNewTab) target="_blank" rel="noopener noreferrer" @endif
               class="bg-white shadow-lg rounded-lg p-4 md:p-6 hover:scale-105 hover:shadow-xl transition-all duration-300 group min-h-[120px] flex items-center justify-center">
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 mb-3 flex items-center justify-center"
                         @if($link->color) style="color: {{ $link->color }};" @else style="color: #10b981;" @endif>
                        @if($link->icon_class)
                            @svg($link->icon_class, 'w-full h-full')
                        @else
                            {{-- Default icon if none specified --}}
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        @endif
                    </div>
                    <h3 class="text-sm md:text-base font-semibold text-gray-800 group-hover:text-emerald-600 transition">
                        {{ $link->label }}
                    </h3>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Statistik Section -->
    @if($statistics->count() > 0)
    <section id="statistik" class="py-12 md:py-16 lg:py-20 bg-gradient-to-br from-blue-50 via-emerald-50 to-teal-50">
        <div class="container mx-auto px-4 md:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-10 md:mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-gray-900">
                    Statistik Desa {{ $settings->site_name ?? 'Donoharjo' }}
                </h2>
                <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">
                    Data demografi dan wilayah terbaru yang menggambarkan kondisi {{ $settings->site_name ?? 'Desa Donoharjo' }}.
                </p>
            </div>
        
        @php
            $categoryLabels = [
                'demografi' => 'Statistik Penduduk',
                'geografis' => 'Statistik Wilayah',
                'ekonomi' => 'Statistik Ekonomi',
                'infrastruktur' => 'Statistik Infrastruktur',
                'sosial' => 'Statistik Sosial',
                'lainnya' => 'Statistik Lainnya',
            ];
            
            // Icon mapping untuk statistik (Heroicons v2 outline)
            $iconMap = [
                'heroicon-o-users' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
                'heroicon-o-home' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
                'heroicon-o-map' => 'M9 20.25l-6.22-3.846a.75.75 0 01-.03-1.28l6.22-3.846a.75.75 0 01.78 0l6.22 3.846a.75.75 0 01-.03 1.28L9 20.25zm0 0l6.22 3.846a.75.75 0 00.78 0l6.22-3.846a.75.75 0 00-.03-1.28L15.78 12.5a.75.75 0 00-.78 0L9 16.346zm0 0l-6.22-3.846a.75.75 0 010-1.28L9 8.654l6.22 3.846a.75.75 0 010 1.28L9 20.25z',
                'heroicon-o-user-group' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.645-5.888-1.654A11.97 11.97 0 014.5 19.5m6.5-1.5a9.094 9.094 0 003.741.479 3 3 0 004.682-2.72m.94 3.198a11.944 11.944 0 01-5.888 1.654c-2.17 0-4.207-.645-5.888-1.654M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.645-5.888-1.654A11.97 11.97 0 014.5 19.5m6.5-1.5a9.094 9.094 0 003.741.479 3 3 0 004.682-2.72m.94 3.198a11.944 11.944 0 01-5.888 1.654c-2.17 0-4.207-.645-5.888-1.654M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.645-5.888-1.654A11.97 11.97 0 014.5 19.5m6.5-1.5a9.094 9.094 0 003.741.479 3 3 0 004.682-2.72m.94 3.198a11.944 11.944 0 01-5.888 1.654c-2.17 0-4.207-.645-5.888-1.654M12 9a3 3 0 100-6 3 3 0 000 6z',
                'heroicon-o-chart-bar' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
            ];
            $defaultIconPath = 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z';
        @endphp

        <!-- Statistics by Category -->
        @foreach($statistics as $category => $categoryStats)
            @php
                $categoryName = $categoryLabels[$category] ?? 'Statistik ' . ucfirst($category);
            @endphp
            
            <!-- Category Section -->
            <div class="mb-10 md:mb-12">
                <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">{{ $categoryName }}</h3>
                
                <!-- Statistics Grid: 1 col (mobile), 2 col (tablet), 4 col (desktop) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach($categoryStats as $stat)
                        @php
                            $iconPath = $stat->icon ? ($iconMap[$stat->icon] ?? $defaultIconPath) : $defaultIconPath;
                        @endphp
                        @include('components.cards.stat-card', [
                            'title' => $stat->label,
                            'value' => $stat->value,
                            'icon' => $iconPath,
                            'iconColor' => 'text-emerald-600',
                        ])
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- CTA Section -->
        <div class="mt-10 md:mt-12 text-center">
                <a href="{{ route('statistik-lengkap') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    Lihat Statistik Lengkap
                </a>
        </div>
        </div>
    </section>
    @endif

    <!-- Sambutan Lurah Section -->
    @if($lurah)
    <x-sections.section spacing="py-12 md:py-16 lg:py-20">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-6 lg:gap-8 items-center">
                <!-- Photo -->
                <div class="flex justify-center md:justify-start">
                    @if($lurah->photo)
                        <img src="{{ str_starts_with($lurah->photo, 'http://') || str_starts_with($lurah->photo, 'https://') ? $lurah->photo : Storage::url($lurah->photo) }}" 
                             alt="{{ $lurah->name }}" 
                             width="400"
                             height="400"
                             class="w-full max-w-md aspect-square object-cover rounded-lg shadow-lg"
                             loading="lazy"
                             decoding="async">
                    @else
                        <div class="w-full max-w-md aspect-square bg-gray-200 rounded-lg shadow-lg flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <!-- Content -->
                <div class="text-center md:text-left">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 text-gray-900">Sambutan {{ $lurah->position }}</h2>
                    <h3 class="text-xl md:text-2xl font-semibold mb-2 text-gray-800">{{ $lurah->name }}</h3>
                    <div class="text-base md:text-lg lg:text-xl text-gray-600 leading-relaxed prose prose-lg max-w-none">
                        @if($lurah->greeting)
                            {!! str($lurah->greeting)->sanitizeHtml() !!}
                        @else
                            <p>
                        Assalamu'alaikum Warahmatullahi Wabarakatuh. 
                        Selamat datang di website resmi {{ $settings->site_name ?? 'Desa Donoharjo' }}. 
                        Melalui website ini, kami berharap dapat memberikan informasi yang akurat dan transparan kepada seluruh masyarakat desa. 
                        Semoga website ini dapat menjadi jembatan komunikasi yang efektif antara pemerintah desa dan masyarakat.
                    </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-sections.section>
    @endif

    <!-- Berita Terkini Section -->
    <x-sections.section 
        id="berita"
        title="Berita Terkini"
        background="bg-gray-50"
        spacing="py-12 md:py-16 lg:py-20">
        
        @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-6 lg:gap-8">
            @foreach($posts as $post)
            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                @if($post->thumbnail)
                <a href="{{ route('post.show', $post->slug) }}">
                    <img src="{{ str_starts_with($post->thumbnail, 'http://') || str_starts_with($post->thumbnail, 'https://') ? $post->thumbnail : Storage::url($post->thumbnail) }}" 
                         alt="{{ $post->title }}" 
                         class="w-full aspect-video object-cover"
                         width="800"
                         height="450"
                         loading="lazy"
                         decoding="async">
                </a>
                @else
                <div class="w-full aspect-video bg-gray-200 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                @endif
                <div class="p-4 md:p-6">
                    <time class="text-sm text-gray-500 mb-2 block">
                        {{ $post->published_at ? $post->published_at->locale('id')->isoFormat('D MMMM YYYY') : '' }}
                    </time>
                    <h3 class="text-lg md:text-xl lg:text-2xl font-bold mb-2 text-gray-900 line-clamp-2">
                        <a href="{{ route('post.show', $post->slug) }}" class="hover:text-emerald-600 transition">{{ $post->title }}</a>
                    </h3>
                    <p class="text-sm md:text-base text-gray-600 line-clamp-3">
                        {{ Str::limit(strip_tags($post->content), 150) }}
                    </p>
                </div>
            </article>
            @endforeach
        </div>
        
        <!-- Lihat Selengkapnya Button -->
        <div class="text-center mt-8 md:mt-10 lg:mt-12">
            <a href="{{ route('berita') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                Lihat Semua Berita
            </a>
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-600 text-lg">Belum ada berita yang dipublikasikan.</p>
        </div>
        @endif
    </x-sections.section>

    <!-- Agenda Desa Section -->
    <x-home-agenda :settings="$settings" />

    <!-- Transparansi APBDes Section -->
    <x-sections.section 
        id="transparansi"
        title="Transparansi APBDes"
        spacing="py-12 md:py-16 lg:py-20">
        
        @if($apbdesData)
        <div class="space-y-6 md:space-y-8">
            <div class="mb-6">
                <p class="text-lg md:text-xl lg:text-2xl font-semibold text-gray-700 text-center md:text-left">
                    APBDes {{ $apbdesData['year'] }} – Pelaksanaan (Ringkasan Utama)
                </p>
            </div>

            <!-- Summary Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                @include('components.cards.apbdes-summary-card', [
                    'title' => 'Pendapatan',
                    'realisasi' => $apbdesData['pendapatan']['realisasi'] ?? 0,
                    'anggaran' => $apbdesData['pendapatan']['anggaran'] ?? 0,
                    'percentage' => $apbdesData['pendapatan']['percentage'] ?? 0,
                ])
                
                @include('components.cards.apbdes-summary-card', [
                    'title' => 'Belanja',
                    'realisasi' => $apbdesData['belanja']['realisasi'] ?? 0,
                    'anggaran' => $apbdesData['belanja']['anggaran'] ?? 0,
                    'percentage' => $apbdesData['belanja']['percentage'] ?? 0,
                ])
                
                @include('components.cards.apbdes-summary-card', [
                    'title' => 'Pembiayaan',
                    'realisasi' => $apbdesData['pembiayaan']['realisasi'] ?? 0,
                    'anggaran' => $apbdesData['pembiayaan']['anggaran'] ?? 0,
                    'percentage' => $apbdesData['pembiayaan']['percentage'] ?? 0,
                ])
            </div>
            
            <!-- Lihat Selengkapnya Button -->
            <div class="text-center mt-8 md:mt-10 lg:mt-12">
                <a href="{{ route('apbdes.show', $apbdesData['year']) }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    Lihat Detail APBDes
                </a>
            </div>
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-600 text-lg">Data APBDes belum tersedia.</p>
        </div>
        @endif
    </x-sections.section>
@endsection
