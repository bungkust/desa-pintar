@props([
    'title' => null,
    'subtitle' => null,
])

<div class="bg-white rounded-xl shadow-md p-6 md:p-8 border border-gray-100">
    @if($title || $subtitle)
    <div class="mb-6">
        @if($title)
        <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">{{ $title }}</h3>
        @endif
        @if($subtitle)
        <p class="text-sm md:text-base text-gray-600">{{ $subtitle }}</p>
        @endif
    </div>
    @endif
    
    <div class="overflow-x-auto">
        {{ $slot }}
    </div>
</div>
