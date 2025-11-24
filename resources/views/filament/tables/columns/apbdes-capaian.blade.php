@php
    $record = $getRecord();
    $anggaran = $record->anggaran ?? 0;
    $realisasi = $record->realisasi ?? 0;
    
    if ($anggaran > 0) {
        $percentage = ($realisasi / $anggaran) * 100;
    } else {
        $percentage = 0;
    }
    
    $formattedPercentage = number_format($percentage, 2, ',', '.') . '%';
    
    // Determine badge color
    $badgeColor = match (true) {
        $percentage >= 100 => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        $percentage >= 80 => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
        default => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    };
    
    // Determine progress bar color
    $progressColor = match (true) {
        $percentage >= 100 => 'bg-green-500',
        $percentage >= 80 => 'bg-yellow-500',
        default => 'bg-red-500',
    };
@endphp

<div class="flex flex-col gap-1.5">
    <span class="px-2 py-1 rounded text-xs font-medium {{ $badgeColor }}">
        {{ $formattedPercentage }}
    </span>
    <div class="w-full bg-gray-200 dark:bg-gray-700 h-2 rounded overflow-hidden">
        <div 
            class="{{ $progressColor }} h-full rounded transition-all duration-300"
            style="width: {{ min($percentage, 100) }}%"
        ></div>
    </div>
</div>

