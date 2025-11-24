@props([
    'title' => null,
    'subtitle' => null,
    'chartId',
    'height' => 'h-64 md:h-80',
])

<div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100">
    @if($title)
    <div class="mb-6">
        <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">{{ $title }}</h3>
        @if($subtitle)
        <p class="text-sm md:text-base text-gray-600">{{ $subtitle }}</p>
        @endif
    </div>
    @endif
    <div class="relative {{ $height }}">
        <canvas id="{{ $chartId }}" aria-label="{{ $title ?? 'Chart' }}"></canvas>
    </div>
</div>