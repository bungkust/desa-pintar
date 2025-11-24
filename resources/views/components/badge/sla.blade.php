@props(['complaint'])

@php
    $slaBadge = null;
    if ($complaint->sla_deadline) {
        $now = now();
        $deadline = $complaint->sla_deadline;
        $diffDays = $now->diffInDays($deadline, false);
        
        if ($complaint->isOverdue()) {
            // Overdue (negative days)
            $daysOverdue = abs($diffDays);
            $slaBadge = [
                'text' => "Overdue by {$daysOverdue} " . ($daysOverdue == 1 ? 'day' : 'days'),
                'color' => 'bg-red-500 text-white',
            ];
        } elseif ($diffDays < 2) {
            // Due soon (< 2 days)
            $slaBadge = [
                'text' => "Due in {$diffDays} " . ($diffDays == 1 ? 'day' : 'days'),
                'color' => 'bg-yellow-400 text-yellow-900',
            ];
        } elseif ($diffDays == 2) {
            // Exactly 2 days
            $slaBadge = [
                'text' => "Due in 2 days",
                'color' => 'bg-green-500 text-white',
            ];
        } else {
            // Normal (> 2 days)
            $slaBadge = [
                'text' => "{$diffDays} days remaining",
                'color' => 'bg-green-500 text-white',
            ];
        }
    }
@endphp

@if($slaBadge)
    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $slaBadge['color'] }}">
        {{ $slaBadge['text'] }}
    </span>
@endif

