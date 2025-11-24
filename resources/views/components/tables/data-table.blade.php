@props([
    'headers' => [],
    'rows' => [],
    'headerBg' => 'bg-emerald-50',
    'emptyMessage' => 'Belum ada data yang tersedia.',
])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
        <thead class="{{ $headerBg }}">
            <tr>
                @foreach($headers as $header)
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">
                    {{ $header }}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($rows as $row)
            <tr class="hover:bg-gray-50">
                @foreach($row as $cell)
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {!! $cell !!}
                </td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($headers) }}" class="px-6 py-8 text-center text-gray-500">
                    {{ $emptyMessage }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
