@extends('layouts.app')

@section('content')
<x-sections.page-header 
    title="Pengaduan Masyarakat"
    description="Laporkan masalah atau keluhan Anda kepada pemerintah desa. Kami siap membantu menyelesaikan masalah Anda dengan cepat dan transparan."
    gradient="from-blue-50 via-emerald-50 to-teal-50"
/>

<!-- Mini Statistics Section -->
@if(isset($stats))
<x-sections.section spacing="py-12 md:py-16" background="bg-white">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
            @include('components.cards.stat-card', [
                'title' => 'Total Pengaduan',
                'value' => $stats['total'] ?? 0,
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'iconColor' => 'text-emerald-600',
                'gradient' => 'from-emerald-50 to-teal-50',
            ])
            @include('components.cards.stat-card', [
                'title' => 'Selesai',
                'value' => $stats['selesai'] ?? 0,
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'iconColor' => 'text-green-600',
                'gradient' => 'from-green-50 to-emerald-50',
            ])
            @include('components.cards.stat-card', [
                'title' => 'Sedang Diproses',
                'value' => $stats['sedang_diproses'] ?? 0,
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'iconColor' => 'text-yellow-600',
                'gradient' => 'from-yellow-50 to-orange-50',
            ])
        </div>
    </div>
</x-sections.section>
@endif

<!-- Process Stepper Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-gray-50">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-12">Proses Pengaduan</h2>
        
        <div class="relative">
            <!-- Desktop: Horizontal stepper with connectors -->
            <div class="hidden md:flex items-center justify-between relative">
                <!-- Step 1 -->
                <div class="flex flex-col items-center text-center flex-1 relative z-10">
                    <div class="w-16 h-16 flex items-center justify-center rounded-full bg-emerald-600 text-white font-bold text-xl shadow-lg mb-4">
                        1
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Kirim</h3>
                    <p class="text-gray-600 text-sm">Isi form pengaduan dengan lengkap</p>
                </div>

                <!-- Connector 1 -->
                <div class="flex-1 h-0.5 bg-gray-300 -mx-4 relative z-0"></div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center text-center flex-1 relative z-10">
                    <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 font-bold text-xl shadow-lg mb-4">
                        2
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Verifikasi</h3>
                    <p class="text-gray-600 text-sm">Tim memverifikasi laporan Anda</p>
                </div>

                <!-- Connector 2 -->
                <div class="flex-1 h-0.5 bg-gray-300 -mx-4 relative z-0"></div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center text-center flex-1 relative z-10">
                    <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 font-bold text-xl shadow-lg mb-4">
                        3
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Penanganan</h3>
                    <p class="text-gray-600 text-sm">Petugas menangani laporan Anda</p>
                </div>

                <!-- Connector 3 -->
                <div class="flex-1 h-0.5 bg-gray-300 -mx-4 relative z-0"></div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center text-center flex-1 relative z-10">
                    <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 font-bold text-xl shadow-lg mb-4">
                        4
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Selesai</h3>
                    <p class="text-gray-600 text-sm">Pengaduan selesai ditangani</p>
                </div>
            </div>

            <!-- Mobile: Vertical stepper -->
            <div class="md:hidden space-y-8">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 flex items-center justify-center rounded-full bg-emerald-600 text-white font-bold text-xl shadow-lg mb-4">
                        1
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Kirim</h3>
                    <p class="text-gray-600 text-sm">Isi form pengaduan dengan lengkap</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 font-bold text-xl shadow-lg mb-4">
                        2
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Verifikasi</h3>
                    <p class="text-gray-600 text-sm">Tim memverifikasi laporan Anda</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 font-bold text-xl shadow-lg mb-4">
                        3
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Penanganan</h3>
                    <p class="text-gray-600 text-sm">Petugas menangani laporan Anda</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 font-bold text-xl shadow-lg mb-4">
                        4
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Selesai</h3>
                    <p class="text-gray-600 text-sm">Pengaduan selesai ditangani</p>
                </div>
            </div>
        </div>
    </div>
</x-sections.section>

<!-- Why Report Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-white">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-12">Kenapa Perlu Melapor?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Cepat & Transparan</h3>
                <p class="text-gray-600 text-sm">Proses cepat dengan update status yang transparan</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Terlacak</h3>
                <p class="text-gray-600 text-sm">Setiap pengaduan memiliki kode tracking unik</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Responsif</h3>
                <p class="text-gray-600 text-sm">Tim kami siap menanggapi setiap pengaduan</p>
            </div>
        </div>
    </div>
</x-sections.section>

<!-- What Can Be Reported Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-gray-50">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-12">Apa Saja yang Bisa Dilaporkan?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col items-center text-center">
                <div class="w-16 h-16 mb-4 text-emerald-600">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Jalan Rusak / Berlubang</h3>
                <p class="text-gray-600 text-sm">Laporkan kondisi jalan yang rusak atau berlubang</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col items-center text-center">
                <div class="w-16 h-16 mb-4 text-emerald-600">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Sampah Menumpuk</h3>
                <p class="text-gray-600 text-sm">Laporkan tumpukan sampah yang mengganggu</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col items-center text-center">
                <div class="w-16 h-16 mb-4 text-emerald-600">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Lampu Jalan Mati</h3>
                <p class="text-gray-600 text-sm">Laporkan lampu jalan yang tidak menyala</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col items-center text-center">
                <div class="w-16 h-16 mb-4 text-emerald-600">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Gangguan Keamanan</h3>
                <p class="text-gray-600 text-sm">Laporkan gangguan keamanan di lingkungan</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col items-center text-center">
                <div class="w-16 h-16 mb-4 text-emerald-600">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Pipa Bocor</h3>
                <p class="text-gray-600 text-sm">Laporkan pipa air yang bocor atau rusak</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col items-center text-center">
                <div class="w-16 h-16 mb-4 text-emerald-600">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12H18.75m-3.75-3.75H16.5m-2.25-2.25h-1.5m-2.25 0H9.75m-1.591 1.591l-1.591-1.591M3 12H5.25m3.75 3.75H5.25m3.75-3.75H9.75m-1.591-1.591l-1.591 1.591M12 18.75V21m0 0h2.25m-2.25 0h-2.25m0 0H9.75m0 0H7.5m0 0h-1.5m1.5 0v-2.25m0 2.25H5.25m3.75-3.75H5.25m3.75 3.75H9.75m-1.591-1.591l-1.591 1.591M12 5.25V3m0 0H9.75m2.25 0H16.5m-2.25 0h-1.5m0 0H12m0 0v2.25m0-2.25v2.25m0 0h-2.25m2.25 0h2.25"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-2">Pohon Tumbang</h3>
                <p class="text-gray-600 text-sm">Laporkan pohon yang tumbang atau berbahaya</p>
            </div>
        </div>
    </div>
</x-sections.section>


<!-- FAQ Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-gray-50">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-12">Pertanyaan Umum</h2>
        <div class="max-w-4xl mx-auto space-y-4">
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="font-bold text-lg text-gray-900 mb-2">Apakah bisa anonim?</h3>
                <p class="text-gray-700">Bisa, tapi nomor WhatsApp tetap diperlukan untuk komunikasi dan update status pengaduan.</p>
            </div>
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="font-bold text-lg text-gray-900 mb-2">Berapa lama diproses?</h3>
                <p class="text-gray-700">1–3 hari sesuai jenis laporan. Pengaduan darurat akan diprioritaskan.</p>
            </div>
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="font-bold text-lg text-gray-900 mb-2">Bagaimana cara melacak?</h3>
                <p class="text-gray-700">Gunakan kode tracking yang diberikan setelah pengaduan dikirim. Kode tracking akan dikirim melalui WhatsApp.</p>
            </div>
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="font-bold text-lg text-gray-900 mb-2">Bagaimana jika laporan saya tidak valid?</h3>
                <p class="text-gray-700">Tim akan memverifikasi setiap laporan. Jika tidak valid, Anda akan diberitahu melalui WhatsApp dengan alasan penolakan.</p>
            </div>
        </div>
    </div>
</x-sections.section>

<!-- Emergency Contact Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-white">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <div class="bg-emerald-50 rounded-xl shadow-lg p-8 md:p-12 text-center border border-emerald-100">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Butuh Bantuan Cepat?</h2>
            <p class="text-gray-600 mb-6 text-lg">Hubungi kami langsung melalui WhatsApp</p>
            <a href="https://wa.me/{{ $settings->whatsapp ?? '6282330462234' }}" 
               target="_blank"
               class="inline-flex items-center px-8 py-4 bg-green-500 hover:bg-green-600 text-white font-bold text-lg rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                📞 Telepon/WhatsApp Desa
            </a>
            <p class="mt-4 text-lg font-semibold text-gray-700">{{ $settings->whatsapp ?? '6282330462234' }}</p>
        </div>
    </div>
</x-sections.section>

<!-- Sticky Mobile CTA Button -->
<a href="{{ route('complaints.form') }}" 
   class="fixed bottom-4 right-4 bg-emerald-600 text-white px-6 py-4 rounded-full shadow-lg hover:bg-emerald-700 transition-colors z-50 md:hidden flex items-center gap-2">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
    </svg>
    <span class="font-bold">Buat Pengaduan</span>
</a>
@endsection

