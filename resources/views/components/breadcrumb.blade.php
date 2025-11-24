@props([
    'items' => [],
])

@if(count($items) > 0)
<nav aria-label="Breadcrumb" class="mb-6">
    <ol class="flex items-center space-x-2 text-sm text-gray-600">
        @foreach($items as $index => $item)
        @if($index > 0)
        <li>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
        </li>
        @endif
        <li>
            @if(isset($item['url']) && !$loop->last)
            <a href="{{ $item['url'] }}" class="hover:text-emerald-600 transition">
                {{ $item['label'] }}
            </a>
            @else
            <span class="{{ $loop->last ? 'text-gray-900 font-semibold' : '' }}" {{ $loop->last ? 'aria-current="page"' : '' }}>
                {{ $item['label'] }}
            </span>
            @endif
        </li>
        @endforeach
    </ol>
</nav>
@endif
