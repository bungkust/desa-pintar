@props([
    'user' => null,
    'size' => 'sm',
])

@php
    $sizeClasses = [
        'sm' => 'w-6 h-6 text-[10px]',
        'md' => 'w-7 h-7 text-xs',
        'lg' => 'w-8 h-8 text-sm',
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['sm'];
    
    $initials = '?';
    $name = null;
    
    if ($user) {
        $name = $user->name;
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr($name, 0, 2));
        }
    }
@endphp

@if($user)
    <div 
        class="{{ $sizeClass }} rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold shadow-sm"
        title="{{ $name }}"
    >
        {{ $initials }}
    </div>
@else
    <div 
        class="{{ $sizeClass }} rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 dark:text-gray-500 font-medium"
        title="Belum ditugaskan"
    >
        ?
    </div>
@endif

