@extends('layouts.app')

@section('content')
    <x-sections.page-header 
        title="Survey Indeks Kepuasan Masyarakat (IKM)"
        description="Berikan masukan dan penilaian Anda terhadap pelayanan publik di {{ $settings->site_name ?? 'Desa Donoharjo' }}"
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-6 flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Tentang Survey IKM</h2>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                Survey Indeks Kepuasan Masyarakat (IKM) adalah kegiatan untuk mengukur tingkat kepuasan masyarakat 
                                terhadap kualitas pelayanan publik yang diberikan oleh Pemerintah Desa Donoharjo.
                            </p>
                            <p class="text-gray-600 leading-relaxed">
                                Pendapat dan masukan Anda sangat penting untuk meningkatkan kualitas pelayanan desa. 
                                Hasil survey ini akan digunakan sebagai bahan evaluasi dan perbaikan pelayanan di masa depan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Aspek yang Dinilai
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Kemudahan prosedur pelayanan</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Kecepatan waktu pelayanan</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Kualitas produk pelayanan</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Kompetensi petugas pelayanan</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Perilaku petugas pelayanan</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Kenyamanan lingkungan pelayanan</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Manfaat Survey
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Meningkatkan kualitas pelayanan publik</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Mengetahui harapan dan kebutuhan masyarakat</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Meningkatkan akuntabilitas penyelenggaraan pelayanan</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Menjadi bahan evaluasi dan perbaikan berkelanjutan</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-6 mb-8">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-emerald-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Informasi Penting</h3>
                        <p class="text-gray-700 mb-2">
                            Survey ini bersifat anonim dan kerahasiaan identitas responden dijamin. Waktu pengisian survey 
                            hanya membutuhkan sekitar 5-10 menit. Kami sangat menghargai partisipasi Anda dalam survey ini.
                        </p>
                        <p class="text-gray-700">
                            Hasil survey akan dipublikasikan di website desa dan menjadi acuan untuk perbaikan pelayanan di tahun berikutnya.
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="https://docs.google.com/forms/d/e/1FAIpQLSdONOHARJO_SURVEY_IKM/viewform" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="inline-flex items-center px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Isi Survey IKM
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>
        </div>
    </x-sections.section>
@endsection

