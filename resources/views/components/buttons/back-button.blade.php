@props([
    'href' => '/',
    'label' => 'Kembali',
    'variant' => 'gray',
])

@php
    $variantClasses = [
        'gray' => 'bg-gray-100 text-gray-700 hover:bg-gray-200',
        'blue' => 'bg-emerald-600 text-white hover:bg-emerald-700',
        'green' => 'bg-emerald-600 text-white hover:bg-emerald-700',
    ];
    $classes = $variantClasses[$variant] ?? $variantClasses['gray'];
@endphp

<a href="{{ $href }}" 
   class="inline-flex items-center justify-center px-6 py-3 {{ $classes }} font-semibold rounded-lg transition-colors shadow-md hover:shadow-lg">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
    </svg>
    {{ $label }}
</a>
