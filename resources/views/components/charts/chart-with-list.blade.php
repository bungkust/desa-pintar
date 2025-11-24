@props([
    'title' => null,
    'subtitle' => null,
    'chartId',
    'height' => 'h-64 md:h-80',
    'items' => [], // Array of items with 'category' and 'realisasi' keys
    'listTitle' => null,
    'listColor' => 'text-emerald-600', // Color for amounts in list
])

<div class="bg-white shadow rounded-xl p-6 border border-gray-100">
    @if($title)
    <h2 class="text-xl font-semibold mb-4 text-gray-800">{{ $title }}</h2>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- LEFT SIDE: PIE CHART -->
        <div class="flex items-center justify-center">
            <div class="w-full max-w-[280px] {{ $height }}">
                <canvas id="{{ $chartId }}" aria-label="{{ $title ?? 'Chart' }}"></canvas>
            </div>
        </div>

        <!-- RIGHT SIDE: TEXT LIST -->
        @if(count($items) > 0)
        <div>
            @if($listTitle)
            <h3 class="text-lg font-semibold mb-3 text-gray-800">{{ $listTitle }}</h3>
            @endif

            <ul class="space-y-2 text-sm">
                @foreach($items as $item)
                <li class="flex justify-between border-b pb-1">
                    <span class="text-gray-900">{{ $item['category'] ?? '' }}</span>
                    <span class="font-medium {{ $listColor }} whitespace-nowrap">
                        Rp {{ number_format($item['realisasi'] ?? 0, 0, ',', '.') }}
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
