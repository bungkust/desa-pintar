@extends('layouts.app')

@section('content')
    <x-sections.page-header 
        title="Layanan Surat"
        description="Informasi layanan surat yang tersedia di {{ $settings->site_name ?? 'Desa Donoharjo' }}"
        gradient="from-blue-50 via-emerald-50 to-teal-50"
    />

    <x-sections.section spacing="py-12 md:py-16 lg:py-20">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                @foreach($layananSurat as $layanan)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $layanan['nama'] }}</h3>
                                <p class="text-gray-600 text-sm mb-4">{{ $layanan['deskripsi'] }}</p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Persyaratan:</h4>
                            <ul class="space-y-1">
                                @foreach($layanan['syarat'] as $syarat)
                                <li class="text-sm text-gray-600 flex items-start">
                                    <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $syarat }}
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Waktu Proses:</span>
                                <span class="text-sm font-semibold text-emerald-600">{{ $layanan['waktu'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8 bg-gradient-to-r from-blue-50 to-emerald-50 rounded-lg p-6 border border-blue-200">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Informasi Penting</h4>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Semua dokumen yang diperlukan harus dalam bentuk fotokopi yang telah dilegalisir</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Pengajuan surat dapat dilakukan pada hari kerja (Senin - Jumat, 08:00 - 14:00 WIB)</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Untuk informasi lebih lanjut, silakan hubungi kantor desa di {{ $settings->village_address ?? 'Jl. Parasamya, Donoharjo, Ngaglik, Sleman, DIY 55581' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </x-sections.section>
@endsection

