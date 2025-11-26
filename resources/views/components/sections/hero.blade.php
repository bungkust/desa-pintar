@props([
    'title' => null,
    'subtitle' => null,
    'image' => null,
    'backgroundGradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'showScrollIndicator' => true,
    'height' => 'h-[90vh]',
    'minHeight' => 'min-h-[600px]',
])

@php
    $backgroundStyle = '';
    if (!empty($image)) {
        // If image is already a full URL (http/https) or absolute path (/storage/...), use it directly
        // Otherwise, treat it as relative path and resolve with Storage::url()
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')) {
            $imageUrl = $image;
        } else {
            $imageUrl = Storage::url($image);
        }
        // Background image without overlay - overlay will be added via div
        $backgroundStyle = "background-image: url('{$imageUrl}'); background-size: cover; background-position: center; background-repeat: no-repeat;";
    } else {
        $backgroundStyle = "background: {$backgroundGradient};";
    }
@endphp

@push('styles')
    @if(!empty($image))
        @php
            // If image is already a full URL (http/https) or absolute path (/storage/...), use it directly
            // Otherwise, treat it as relative path and resolve with Storage::url()
            if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')) {
                $imageUrl = $image;
            } else {
                $imageUrl = Storage::url($image);
            }
        @endphp
        <link rel="preload" as="image" href="{{ $imageUrl }}" fetchpriority="high">
    @endif
@endpush

<section class="relative {{ $height }} {{ $minHeight }} flex items-center justify-center text-white overflow-hidden hero-section" 
         style="{{ $backgroundStyle }}">
    <!-- Overlay Gradient - lighter overlay to keep image visible -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-black/30 to-black/40 z-0"></div>
    
    <!-- Content -->
    <div class="container-standard py-20 md:py-24 lg:py-32 text-center relative z-10">
        <div class="max-w-4xl mx-auto">
            @if($title)
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-4 md:mb-6 drop-shadow-lg animate-fade-in leading-none tracking-tight">
                    {{ $title }}
                </h1>
            @endif
            @if($subtitle)
                <p class="text-lg md:text-xl text-white max-w-3xl mx-auto drop-shadow-md animate-fade-in-delay leading-relaxed">
                    {{ $subtitle }}
                </p>
            @endif
            {{ $slot }}
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    @if($showScrollIndicator)
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    @endif
</section>