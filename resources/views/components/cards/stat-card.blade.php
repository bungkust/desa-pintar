@props([
    'title',
    'value',
    'label' => null,
    'icon' => null,
    'iconColor' => 'text-emerald-600',
    'gradient' => 'from-gray-50 to-blue-50',
])

<div class="bg-gradient-to-br {{ $gradient }} rounded-xl shadow-md p-6 text-center border border-gray-100 hover:shadow-lg transition-all duration-300">
    @if($icon)
    <div class="w-10 h-10 mx-auto mb-4 {{ $iconColor }}">
        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
        </svg>
    </div>
    @endif
    <div class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">{{ $value }}</div>
    @if($label)
    <div class="text-sm md:text-base text-gray-600 font-medium">{{ $label }}</div>
    @else
    <div class="text-sm md:text-base text-gray-600 font-medium">{{ $title }}</div>
    @endif
</div>
