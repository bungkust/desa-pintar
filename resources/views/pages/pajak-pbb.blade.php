@extends('layouts.app')

@section('content')
    <x-sections.page-header 
        title="Pajak PBB (Pajak Bumi dan Bangunan)"
        description="Informasi tentang Pajak Bumi dan Bangunan (PBB) di {{ $settings->site_name ?? 'Desa Donoharjo' }}. Cek tagihan, jadwal pembayaran, dan informasi lainnya."
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-6 flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Tentang Pajak PBB</h2>
                            <p class="text-gray-600 leading-relaxed">
                                Pajak Bumi dan Bangunan (PBB) adalah pajak yang dikenakan atas kepemilikan, penguasaan, dan/atau pemanfaatan bumi dan/atau bangunan. 
                                PBB merupakan salah satu sumber pendapatan daerah yang penting untuk pembangunan desa.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Jadwal Pembayaran
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Pembayaran dapat dilakukan mulai bulan <strong>Januari</strong> setiap tahunnya</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Batas akhir pembayaran adalah <strong>akhir September</strong></span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Pembayaran setelah batas waktu akan dikenakan denda 2% per bulan</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Cara Cek Tagihan
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Siapkan NOP (Nomor Objek Pajak) dari SPPT PBB Anda</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Klik tombol di bawah untuk mengakses sistem cek tagihan online</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-emerald-600 mr-2">•</span>
                            <span>Masukkan NOP untuk melihat detail tagihan</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-blue-50 border-l-4 border-emerald-500 p-6 mb-8">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-emerald-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Informasi Penting</h3>
                        <p class="text-gray-700">
                            Untuk informasi lebih lanjut tentang Pajak PBB atau jika mengalami kendala dalam pengecekan tagihan, 
                            silakan menghubungi Kantor Pajak setempat atau datang langsung ke Kantor Desa Donoharjo pada jam kerja.
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="https://pbb.donoharjo.sides.id/PbbSemuaList" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="inline-flex items-center px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                    Cek Tagihan Pajak PBB
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>
        </div>
    </x-sections.section>
@endsection

