@extends('layouts.app')

@section('content')
    <x-sections.page-header 
        title="Peladi Makarti / Ketenagakerjaan"
        description="Informasi tentang layanan ketenagakerjaan dan Peladi Makarti di {{ $settings->site_name ?? 'Desa Donoharjo' }}"
        gradient="from-blue-50 via-emerald-50 to-teal-50"
    />

    <x-sections.section spacing="py-12 md:py-16 lg:py-20">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="p-8">
                    <div class="flex items-start mb-6">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-6 flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Tentang Peladi Makarti</h2>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                Peladi Makarti (Pelayanan Terpadu Ketenagakerjaan) adalah program layanan ketenagakerjaan terpadu yang memberikan 
                                berbagai informasi dan layanan terkait kesempatan kerja, pelatihan, serta bantuan untuk pencari kerja dan pengusaha.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                Program ini dikelola oleh Pemerintah Provinsi DIY bekerja sama dengan Pemerintah Desa untuk meningkatkan 
                                kualitas sumber daya manusia dan mengurangi pengangguran di wilayah desa.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Layanan untuk Pencari Kerja
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Pendaftaran lowongan kerja terbaru</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Konsultasi karir dan bimbingan kerja</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Pelatihan kerja dan pengembangan skill</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Bantuan dalam pembuatan CV dan surat lamaran</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Layanan untuk Pengusaha
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Pendaftaran lowongan kerja</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Pencarian tenaga kerja terampil</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Informasi program magang dan PKL</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Konsultasi ketenagakerjaan dan peraturan</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Program Pelatihan yang Tersedia
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-emerald-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Pelatihan Komputer</h4>
                        <p class="text-sm text-gray-600">Microsoft Office, Desain Grafis, dll</p>
                    </div>
                    <div class="bg-emerald-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Pelatihan Wirausaha</h4>
                        <p class="text-sm text-gray-600">Kewirausahaan, Pemasaran Digital</p>
                    </div>
                    <div class="bg-emerald-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Pelatihan Ketrampilan</h4>
                        <p class="text-sm text-gray-600">Kerajinan, Kuliner, Teknik</p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border-l-4 border-emerald-500 p-6 mb-8">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-emerald-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Kontak dan Informasi</h3>
                        <p class="text-gray-700 mb-2">
                            Untuk informasi lebih lanjut tentang layanan ketenagakerjaan, silakan mengakses sistem Peladi Makarti 
                            atau datang langsung ke Kantor Desa Donoharjo pada jam kerja.
                        </p>
                        <p class="text-gray-700">
                            <strong>Jam Layanan:</strong> Senin - Jumat, 08:00 - 14:00 WIB
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="https://sinkal.jogjaprov.go.id/donoharjo/layanan-ketenagakerjaan" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="inline-flex items-center px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Kunjungi Layanan Ketenagakerjaan
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>
        </div>
    </x-sections.section>
@endsection

