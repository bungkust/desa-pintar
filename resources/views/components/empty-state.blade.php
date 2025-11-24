@props([
    'title' => 'Data Belum Tersedia',
    'message' => 'Belum ada data yang tersedia saat ini.',
    'icon' => null,
    'action' => null,
])

<div class="bg-white rounded-xl shadow-md p-8 md:p-12 text-center">
    <div class="max-w-md mx-auto">
        @if($icon)
        {!! $icon !!}
        @else
        <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        @endif
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">{{ $title }}</h1>
        <p class="text-gray-600 mb-6">{{ $message }}</p>
        @if($action)
        <div class="mt-6">
            {!! $action !!}
        </div>
        @endif
    </div>
</div>
