@props([
    'heroTitle' => null,
    'heroSubtitle' => null,
    'heroImage' => null,
    'heroSlide' => null, // Alternative: pass HeroSlide model
    'heroShowScrollIndicator' => true,
    'quickLinks' => [],
    'showQuickLinks' => true,
    'settings' => null, // For fallback title
])

@php
    // Determine hero title and subtitle
    $finalHeroTitle = $heroTitle ?? ($heroSlide->title ?? null) ?? (($settings->site_name ?? null) ? ('Selamat Datang di ' . $settings->site_name) : 'Selamat Datang');
    $finalHeroSubtitle = $heroSubtitle ?? ($heroSlide->subtitle ?? null) ?? 'Website resmi desa kami';
    
    // Handle hero image URL - Better null/empty checking
    $heroImageUrl = null;
    if (!empty($heroImage)) {
        $heroImageUrl = str_starts_with($heroImage, 'http://') || str_starts_with($heroImage, 'https://') 
            ? $heroImage 
            : Storage::url($heroImage);
    } elseif ($heroSlide && !empty($heroSlide->image)) {
        $heroImageUrl = str_starts_with($heroSlide->image, 'http://') || str_starts_with($heroSlide->image, 'https://') 
            ? $heroSlide->image 
            : Storage::url($heroSlide->image);
    }
@endphp

@push('styles')
    @if(!empty($heroImageUrl))
    <link rel="preload" as="image" href="{{ $heroImageUrl }}" fetchpriority="high">
    @endif
@endpush

{{-- TEMPORARY DEBUG - Remove after fixing --}}
@if(config('app.debug'))
    <!-- DEBUG: heroSlide exists = {{ $heroSlide ? 'YES' : 'NO' }} -->
    @if($heroSlide)
        <!-- DEBUG: heroSlide->id = {{ $heroSlide->id }} -->
        <!-- DEBUG: heroSlide->is_active = {{ $heroSlide->is_active ? 'YES' : 'NO' }} -->
        <!-- DEBUG: heroSlide->image = {{ $heroSlide->image ?? 'NULL' }} -->
        <!-- DEBUG: heroSlide->image empty = {{ empty($heroSlide->image) ? 'YES' : 'NO' }} -->
    @endif
    <!-- DEBUG: heroImageUrl = {{ $heroImageUrl ?? 'NULL' }} -->
@endif

<!-- Hero Section -->
@if($finalHeroTitle || $finalHeroSubtitle)
    <x-sections.hero 
        :title="$finalHeroTitle"
        :subtitle="$finalHeroSubtitle"
        :image="$heroImageUrl"
        :showScrollIndicator="$heroShowScrollIndicator" />
@endif

<!-- Quick Links Section -->
@if($showQuickLinks)
    @php
        // Ensure quickLinks is a collection or array
        $quickLinksCollection = $quickLinks ?? collect([]);
        if (!($quickLinksCollection instanceof \Illuminate\Support\Collection)) {
            $quickLinksCollection = collect($quickLinksCollection ?? []);
        }
    @endphp
    @if($quickLinksCollection->count() > 0)
        <x-sections.quicklinks :quickLinks="$quickLinksCollection" />
    @endif
@endif

<!-- Main Content Sections -->
{{ $slot }}
