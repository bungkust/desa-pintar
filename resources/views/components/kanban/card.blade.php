@props(['complaint'])

@php
    // Category icons and colors
    $categoryConfig = [
        'infrastruktur' => ['icon' => '🛠', 'name' => 'Infrastruktur'],
        'sampah' => ['icon' => '🗑', 'name' => 'Sampah'],
        'air' => ['icon' => '💧', 'name' => 'Air'],
        'listrik' => ['icon' => '⚡', 'name' => 'Listrik'],
        'keamanan' => ['icon' => '🛡', 'name' => 'Keamanan'],
        'kesehatan' => ['icon' => '❤️', 'name' => 'Kesehatan'],
        'pendidikan' => ['icon' => '🎓', 'name' => 'Pendidikan'],
        'sosial' => ['icon' => '🤝', 'name' => 'Sosial'],
        'lainnya' => ['icon' => '📄', 'name' => 'Lainnya'],
    ];
    
    $category = $categoryConfig[$complaint->category] ?? $categoryConfig['lainnya'];
    
    // Calculate priority
    $priority = 'medium';
    if ($complaint->isOverdue()) {
        $priority = 'high';
    } elseif ($complaint->isNearingDeadline()) {
        $priority = 'high';
    } elseif (in_array($complaint->status, ['verification', 'todo'])) {
        $priority = 'medium';
    } else {
        $priority = 'low';
    }
@endphp

<div 
    class="jira-card bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 p-3 cursor-move border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600"
    data-complaint-id="{{ $complaint->id }}"
    data-status="{{ $complaint->status }}"
    draggable="true"
>
    <!-- Top Row: Code Badge + Priority Badge -->
    <div class="flex items-center justify-between mb-2">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
            {{ $complaint->tracking_code }}
        </span>
        <x-badge.priority :priority="$priority" />
    </div>
    
    <!-- Title (bold) -->
    <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 mb-2 line-clamp-2 leading-tight">
        {{ Str::limit($complaint->title, 60) }}
    </h4>
    
    <!-- Meta Row: Category Icon + Name | RT/RW -->
    <div class="flex items-center justify-between mb-2 pb-2 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-1.5">
            <span class="text-sm">{{ $category['icon'] }}</span>
            <span class="text-[10px] text-gray-600 dark:text-gray-400 font-medium">
                {{ $category['name'] }}
            </span>
        </div>
        @if($complaint->rt || $complaint->rw)
            <span class="text-[10px] text-gray-500 dark:text-gray-500 font-medium">
                RT{{ $complaint->rt ?? '—' }}/RW{{ $complaint->rw ?? '—' }}
            </span>
        @endif
    </div>
    
    <!-- Assignee Row -->
    <div class="flex items-center gap-2 mb-2">
        <x-avatar.user :user="$complaint->assignedUser" size="sm" />
        @if($complaint->assignedUser)
            <span class="text-xs text-gray-600 dark:text-gray-400 truncate max-w-[100px]" title="{{ $complaint->assignedUser->name }}">
                {{ $complaint->assignedUser->name }}
            </span>
        @else
            <span class="text-xs text-gray-400 dark:text-gray-500">Unassigned</span>
        @endif
    </div>
    
    <!-- SLA Badge at Bottom -->
    <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
        <x-badge.sla :complaint="$complaint" />
        <a 
            href="{{ \App\Filament\Resources\ComplaintResource::getUrl('view', ['record' => $complaint]) }}"
            class="text-[10px] text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors"
            onclick="event.stopPropagation()"
        >
            View →
        </a>
    </div>
</div>
