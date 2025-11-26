@extends('layouts.app')

@section('content')
    <x-sections.page-header 
        title="{{ $content['title'] }}"
        description="{{ $content['description'] }}"
        gradient="from-blue-50 via-emerald-50 to-teal-50"
    />

<x-sections.section spacing="py-12 md:py-16 lg:py-20">
    <div class="max-w-4xl mx-auto">

        @if(count($content['items']) > 0)
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-6 rounded-lg mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Fitur yang akan tersedia:</h2>
            <ul class="space-y-2">
                @foreach($content['items'] as $item)
                <li class="flex items-start text-gray-700">
                    <svg class="w-5 h-5 text-emerald-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ $item }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6 md:p-8">
            <div class="prose prose-lg max-w-none">
                <p class="text-gray-700 leading-relaxed mb-4">
                    Kami sedang menyiapkan halaman ini untuk memberikan informasi yang lebih lengkap dan terstruktur kepada masyarakat.
                </p>
                <p class="text-gray-700 leading-relaxed mb-6">
                    Jika Anda memerlukan informasi lebih lanjut, silakan hubungi kami melalui:
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Telepon/WhatsApp</h3>
                            <p class="text-gray-600">{{ $settings->whatsapp ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Alamat</h3>
                            <p class="text-gray-600">{{ $settings->village_address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="/" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</x-sections.section>
@endsection

