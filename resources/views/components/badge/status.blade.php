@props([
    'status',
    'count' => 0,
])

@php
    $statusConfig = [
        'backlog' => [
            'label' => 'Backlog',
            'bg' => 'bg-gray-300',
            'text' => 'text-gray-800',
        ],
        'verification' => [
            'label' => 'Verification',
            'bg' => 'bg-blue-400',
            'text' => 'text-blue-900',
        ],
        'todo' => [
            'label' => 'To Do',
            'bg' => 'bg-sky-400',
            'text' => 'text-sky-900',
        ],
        'in_progress' => [
            'label' => 'In Progress',
            'bg' => 'bg-yellow-400',
            'text' => 'text-yellow-900',
        ],
        'done' => [
            'label' => 'Done',
            'bg' => 'bg-green-400',
            'text' => 'text-green-900',
        ],
        'rejected' => [
            'label' => 'Rejected',
            'bg' => 'bg-red-400',
            'text' => 'text-red-900',
        ],
    ];
    
    $config = $statusConfig[$status] ?? $statusConfig['backlog'];
@endphp

<div class="flex items-center justify-between mb-4">
    <h3 class="font-bold text-sm {{ $config['text'] }} uppercase tracking-wide">
        {{ $config['label'] }}
    </h3>
    <span class="inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full {{ $config['bg'] }} {{ $config['text'] }} text-xs font-bold">
        {{ $count }}
    </span>
</div>

