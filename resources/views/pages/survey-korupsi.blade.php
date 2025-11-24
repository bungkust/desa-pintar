@extends('layouts.app')

@section('content')
    <x-sections.page-header 
        title="Survey Indeks Persepsi Anti Korupsi (SPAK)"
        description="Ikuti survey untuk mengukur persepsi masyarakat tentang upaya pencegahan korupsi di {{ $settings->site_name ?? 'Desa Donoharjo' }}"
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-6 flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Tentang Survey SPAK</h2>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                Survey Indeks Persepsi Anti Korupsi (SPAK) adalah instrumen untuk mengukur persepsi masyarakat 
                                terhadap upaya pencegahan dan pemberantasan korupsi yang dilakukan oleh Pemerintah Desa Donoharjo.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                Survey ini bertujuan untuk menilai efektivitas program anti korupsi, transparansi pengelolaan 
                                keuangan desa, dan akuntabilitas penyelenggaraan pemerintahan desa.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Aspek yang Dinilai
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Transparansi informasi dan pengelolaan dana desa</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Akuntabilitas penyelenggaraan pemerintahan</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Integritas aparatur desa</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Partisipasi masyarakat dalam pengawasan</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Efektivitas sistem pengaduan dan penindakan</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Tujuan Survey
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Mengukur tingkat persepsi masyarakat terhadap anti korupsi</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Mengidentifikasi area yang perlu diperbaiki</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Meningkatkan transparansi dan akuntabilitas</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Membangun kepercayaan masyarakat</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Komitmen Anti Korupsi</h3>
                <p class="text-gray-600 mb-4">
                    Pemerintah Desa Donoharjo berkomitmen untuk melaksanakan tata kelola pemerintahan yang bersih, 
                    transparan, dan bebas dari korupsi. Kami terus berupaya meningkatkan sistem pengawasan internal 
                    dan memberikan ruang partisipasi masyarakat dalam pengawasan.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="bg-emerald-50 p-4 rounded-lg text-center">
                        <svg class="w-10 h-10 text-emerald-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <h4 class="font-semibold text-gray-900 mb-1">Transparan</h4>
                        <p class="text-sm text-gray-600">Informasi terbuka dan mudah diakses</p>
                    </div>
                    <div class="bg-emerald-50 p-4 rounded-lg text-center">
                        <svg class="w-10 h-10 text-emerald-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <h4 class="font-semibold text-gray-900 mb-1">Bersih</h4>
                        <p class="text-sm text-gray-600">Bebas dari praktik korupsi</p>
                    </div>
                    <div class="bg-emerald-50 p-4 rounded-lg text-center">
                        <svg class="w-10 h-10 text-emerald-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <h4 class="font-semibold text-gray-900 mb-1">Akuntabel</h4>
                        <p class="text-sm text-gray-600">Pertanggungjawaban yang jelas</p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border-l-4 border-emerald-500 p-6 mb-8">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-emerald-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Informasi Penting</h3>
                        <p class="text-gray-700 mb-2">
                            Survey ini bersifat anonim dan kerahasiaan identitas responden dijamin penuh. 
                            Partisipasi Anda sangat berarti dalam membangun tata kelola pemerintahan yang lebih baik.
                        </p>
                        <p class="text-gray-700">
                            Hasil survey akan dipublikasikan dan menjadi bahan evaluasi untuk peningkatan program anti korupsi di desa.
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="https://docs.google.com/forms/d/e/1FAIpQLSdONOHARJO_SURVEY_KORUPSI/viewform" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="inline-flex items-center px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Isi Survey SPAK
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>
        </div>
    </x-sections.section>
@endsection

