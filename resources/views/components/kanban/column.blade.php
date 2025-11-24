@props([
    'status',
    'label',
    'count',
    'complaints',
])

@php
    $statusConfig = [
        'backlog' => [
            'label' => 'Backlog',
            'bg' => 'bg-gray-300',
            'text' => 'text-gray-800',
            'columnBg' => 'bg-gray-50 dark:bg-gray-800/50',
        ],
        'verification' => [
            'label' => 'Verification',
            'bg' => 'bg-blue-400',
            'text' => 'text-blue-900',
            'columnBg' => 'bg-blue-50/30 dark:bg-blue-900/10',
        ],
        'todo' => [
            'label' => 'To Do',
            'bg' => 'bg-sky-400',
            'text' => 'text-sky-900',
            'columnBg' => 'bg-sky-50/30 dark:bg-sky-900/10',
        ],
        'in_progress' => [
            'label' => 'In Progress',
            'bg' => 'bg-yellow-400',
            'text' => 'text-yellow-900',
            'columnBg' => 'bg-yellow-50/30 dark:bg-yellow-900/10',
        ],
        'done' => [
            'label' => 'Done',
            'bg' => 'bg-green-400',
            'text' => 'text-green-900',
            'columnBg' => 'bg-green-50/30 dark:bg-green-900/10',
        ],
        'rejected' => [
            'label' => 'Rejected',
            'bg' => 'bg-red-400',
            'text' => 'text-red-900',
            'columnBg' => 'bg-red-50/30 dark:bg-red-900/10',
        ],
    ];
    
    $config = $statusConfig[$status] ?? $statusConfig['backlog'];
@endphp

<div 
    class="jira-column flex-shrink-0 w-80 {{ $config['columnBg'] }} rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm dark:shadow-gray-900/20"
    data-status="{{ $status }}"
    data-column-id="{{ $status }}"
>
    <!-- Column Header -->
    <x-badge.status :status="$status" :count="$count" />
    
    <!-- Cards Container -->
    <div 
        class="jira-cards-container space-y-3 min-h-[200px] mt-4"
        data-column="{{ $status }}"
    >
        @forelse($complaints as $complaint)
            <x-kanban.card :complaint="$complaint" />
        @empty
            <div class="empty-state bg-white/50 dark:bg-gray-700/30 rounded-lg p-6 text-center border-2 border-dashed border-gray-300 dark:border-gray-600 min-h-[120px] flex items-center justify-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Tidak ada pengaduan
                </p>
            </div>
        @endforelse
    </div>
</div>
