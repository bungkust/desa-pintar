@props([
    'href' => '/',
    'label' => 'Kembali',
    'variant' => 'outline', // 'outline', 'primary', 'gray'
])

@php
    $variantClasses = [
        'outline' => 'border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-50 bg-white',
        'primary' => 'bg-emerald-600 text-white hover:bg-emerald-700',
        'gray' => 'bg-gray-100 text-gray-700 hover:bg-gray-200',
    ];
    $classes = $variantClasses[$variant] ?? $variantClasses['outline'];
@endphp

<a href="{{ $href }}" 
   class="inline-flex items-center justify-center px-6 py-3 {{ $classes }} font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
    </svg>
    {{ $label }}
</a>
