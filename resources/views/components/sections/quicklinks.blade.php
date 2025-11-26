@props([
    'quickLinks' => collect([]),
    'links' => null, // Alternative prop name for backward compatibility
    'gridCols' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
    'spacing' => '-mt-16 md:-mt-20 lg:-mt-24',
    'cardMinHeight' => 'min-h-[120px]',
])

@php
    use App\Helpers\QuickLinkHelper;
    
    // Support both prop names
    $linksToUse = $quickLinks ?? $links ?? collect([]);
    
    // Ensure it's a collection
    if (!($linksToUse instanceof \Illuminate\Support\Collection)) {
        $linksToUse = collect($linksToUse ?? []);
    }
@endphp

@if($linksToUse->count() > 0)
<section class="container-standard {{ $spacing }} relative z-10">
    <div class="grid {{ $gridCols }} gap-4 md:gap-4 lg:gap-6">
        @foreach($linksToUse as $link)
            @php
                [$actualUrl, $openInNewTab] = QuickLinkHelper::resolveUrl($link);
            @endphp
            <a href="{{ $actualUrl }}" 
               @if($openInNewTab) target="_blank" rel="noopener noreferrer" @endif
               class="bg-white shadow-lg rounded-lg p-4 md:p-6 hover:scale-105 hover:shadow-xl transition-all duration-300 group {{ $cardMinHeight }} flex items-center justify-center">
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
