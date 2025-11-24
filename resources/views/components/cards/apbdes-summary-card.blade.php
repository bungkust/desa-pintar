@props([
    'title',
    'realisasi',
    'anggaran',
    'percentage',
])

@php
    // Determine color based on percentage
    if ($percentage >= 80) {
        $textColor = 'text-green-600';
        $progressColor = 'bg-green-500';
    } elseif ($percentage >= 50) {
        $textColor = 'text-orange-600';
        $progressColor = 'bg-orange-500';
    } else {
        $textColor = 'text-red-600';
        $progressColor = 'bg-red-500';
    }
@endphp

<div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl shadow-md p-6 text-center border border-gray-100 hover:shadow-lg transition-all duration-300">
    <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-4">{{ $title }}</h3>
    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">Realisasi</p>
            <p class="text-2xl md:text-3xl font-bold {{ $textColor }}">
                Rp {{ number_format($realisasi, 0, ',', '.') }}
            </p>
        </div>
        <div>
            <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">Anggaran</p>
            <p class="text-lg md:text-xl font-semibold text-gray-700">
                Rp {{ number_format($anggaran, 0, ',', '.') }}
            </p>
        </div>
        <div class="pt-3 border-t border-gray-200">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-gray-700">Capaian</span>
                <span class="text-lg font-bold {{ $textColor }}">
                    {{ number_format($percentage, 2, ',', '.') }}%
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 relative overflow-hidden">
                <div class="{{ $progressColor }} h-2 rounded-full transition-all duration-500" 
                     style="width: {{ min(100, max(0, $percentage)) }}%"></div>
            </div>
        </div>
    </div>
</div>
