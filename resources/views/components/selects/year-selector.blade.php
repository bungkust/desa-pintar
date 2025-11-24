@props([
    'currentYear',
    'availableYears' => [],
    'routeName' => 'apbdes.show',
    'label' => 'Pilih Tahun:',
])

@if(count($availableYears) > 1)
<div class="inline-flex items-center gap-4 bg-white rounded-lg p-4 shadow-sm border border-gray-200">
    <label for="year-select" class="text-sm font-semibold text-gray-700 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        {{ $label }}
    </label>
    <select id="year-select" 
            onchange="window.location.href='{{ route($routeName, ['year' => '']) }}/' + this.value" 
            class="border border-gray-300 rounded-lg px-4 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium">
        @foreach($availableYears as $year)
        <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
            {{ $year }}
        </option>
        @endforeach
    </select>
</div>
@endif
