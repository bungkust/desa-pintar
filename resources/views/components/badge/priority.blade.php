@props(['priority'])

@php
    $priorityConfig = [
        'high' => ['text' => 'HIGH', 'color' => 'bg-red-500 text-white'],
        'medium' => ['text' => 'MED', 'color' => 'bg-yellow-500 text-white'],
        'low' => ['text' => 'LOW', 'color' => 'bg-green-500 text-white'],
    ];
    
    $config = $priorityConfig[$priority] ?? $priorityConfig['medium'];
@endphp

<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $config['color'] }}">
    {{ $config['text'] }}
</span>

