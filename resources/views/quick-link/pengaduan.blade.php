@extends('layouts.app')

@section('content')
<x-sections.page-header 
    title="Pengaduan Masyarakat"
    description="Laporkan masalah atau keluhan Anda kepada pemerintah desa. Kami siap membantu menyelesaikan masalah Anda dengan cepat dan transparan."
    gradient="from-emerald-50 via-teal-50 to-cyan-50"
/>

<!-- CTA Section - Large Buttons -->
<x-sections.section spacing="py-8 md:py-12" background="bg-white">
    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
        <a href="{{ route('complaints.index') }}" 
           class="w-full sm:w-auto px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg rounded-lg shadow-md hover:shadow-lg transition-all duration-200 text-center">
            📝 Buat Pengaduan Baru
        </a>
        <a href="{{ route('complaints.tracking-form') }}" 
           class="w-full sm:w-auto px-8 py-4 border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-50 font-bold text-lg rounded-lg shadow-md hover:shadow-lg transition-all duration-200 text-center">
            🔍 Lacak Pengaduan
        </a>
    </div>
</x-sections.section>

<!-- Statistics Section - Transparency -->
@if(isset($stats))
<x-sections.section spacing="py-8 md:py-12" background="bg-gray-50">
    <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-8">Transparansi Pengaduan</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
        @include('components.cards.stat-card', [
            'title' => 'Pengaduan',
            'value' => $stats['total'] ?? 167,
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'iconColor' => 'text-emerald-600',
            'gradient' => 'from-emerald-50 to-teal-50',
        ])
        @include('components.cards.stat-card', [
            'title' => 'Selesai',
            'value' => $stats['selesai'] ?? 153,
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconColor' => 'text-green-600',
            'gradient' => 'from-green-50 to-emerald-50',
        ])
        @include('components.cards.stat-card', [
            'title' => 'Sedang Diproses',
            'value' => $stats['sedang_diproses'] ?? 7,
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconColor' => 'text-yellow-600',
            'gradient' => 'from-yellow-50 to-orange-50',
        ])
    </div>
</x-sections.section>
@endif

<!-- Process Flow Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-white">
    <div class="max-w-5xl mx-auto space-y-8">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900">Proses Pengaduan</h2>

        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
            <!-- Step 1 -->
            <div class="flex flex-col items-center text-center w-full">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-600 text-white font-semibold shadow-md">
                    1
                </div>
                <p class="font-semibold mt-3 text-gray-900">Kirim</p>
                <p class="text-gray-600 text-sm mt-1">Isi form pengaduan dengan lengkap</p>
            </div>

            <!-- Connector -->
            <div class="hidden md:block flex-1 h-0.5 bg-gray-300"></div>

            <!-- Step 2 -->
            <div class="flex flex-col items-center text-center w-full">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 font-semibold shadow-md">
                    2
                </div>
                <p class="font-semibold mt-3 text-gray-900">Verifikasi</p>
                <p class="text-gray-600 text-sm mt-1">Tim memverifikasi laporan Anda</p>
            </div>

            <!-- Connector -->
            <div class="hidden md:block flex-1 h-0.5 bg-gray-300"></div>

            <!-- Step 3 -->
            <div class="flex flex-col items-center text-center w-full">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 font-semibold shadow-md">
                    3
                </div>
                <p class="font-semibold mt-3 text-gray-900">Penanganan</p>
                <p class="text-gray-600 text-sm mt-1">Petugas menangani laporan Anda</p>
            </div>

            <!-- Connector -->
            <div class="hidden md:block flex-1 h-0.5 bg-gray-300"></div>

            <!-- Step 4 -->
            <div class="flex flex-col items-center text-center w-full">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 font-semibold shadow-md">
                    4
                </div>
                <p class="font-semibold mt-3 text-gray-900">Selesai</p>
                <p class="text-gray-600 text-sm mt-1">Pengaduan selesai ditangani</p>
            </div>
        </div>
    </div>
</x-sections.section>

<!-- What Can Be Reported Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-gray-50">
    <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-8">Apa Saja yang Bisa Dilaporkan?</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 mr-3 text-emerald-600 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Jalan Rusak / Berlubang</h3>
                    <p class="text-sm text-gray-600">Laporkan kondisi jalan yang rusak atau berlubang</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 mr-3 text-emerald-600 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Lampu Jalan Mati</h3>
                    <p class="text-sm text-gray-600">Laporkan lampu jalan yang tidak menyala</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 mr-3 text-emerald-600 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Sampah Menumpuk</h3>
                    <p class="text-sm text-gray-600">Laporkan tumpukan sampah yang mengganggu</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 mr-3 text-emerald-600 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Gangguan Keamanan</h3>
                    <p class="text-sm text-gray-600">Laporkan gangguan keamanan di lingkungan</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 mr-3 text-emerald-600 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Pipa Bocor</h3>
                    <p class="text-sm text-gray-600">Laporkan pipa air yang bocor atau rusak</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-start">
                <div class="w-10 h-10 mr-3 text-emerald-600 flex-shrink-0">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12H18.75m-3.75-3.75H16.5m-2.25-2.25h-1.5m-2.25 0H9.75m-1.591 1.591l-1.591-1.591M3 12H5.25m3.75 3.75H5.25m3.75-3.75H9.75m-1.591-1.591l-1.591 1.591M12 18.75V21m0 0h2.25m-2.25 0h-2.25m0 0H9.75m0 0H7.5m0 0h-1.5m1.5 0v-2.25m0 2.25H5.25m3.75-3.75H5.25m3.75 3.75H9.75m-1.591-1.591l-1.591 1.591M12 5.25V3m0 0H9.75m2.25 0H16.5m-2.25 0h-1.5m0 0H12m0 0v2.25m0-2.25v2.25m0 0h-2.25m2.25 0h2.25"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Pohon Tumbang</h3>
                    <p class="text-sm text-gray-600">Laporkan pohon yang tumbang atau berbahaya</p>
                </div>
            </div>
        </div>
    </div>
</x-sections.section>

<!-- Quick Help Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-emerald-50">
    <div class="bg-white rounded-xl shadow-lg p-8 text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Butuh Bantuan Cepat?</h2>
        <p class="text-gray-600 mb-6">Hubungi kami langsung melalui WhatsApp</p>
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
</x-sections.section>

<!-- FAQ Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-white">
    <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-8">Pertanyaan Umum</h2>
    <div class="max-w-4xl mx-auto space-y-4">
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="font-bold text-lg text-gray-900 mb-2">• Apakah bisa anonim?</h3>
            <p class="text-gray-700">Bisa, tapi nomor WhatsApp tetap diperlukan untuk komunikasi dan update status pengaduan.</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="font-bold text-lg text-gray-900 mb-2">• Berapa lama diproses?</h3>
            <p class="text-gray-700">1–3 hari sesuai jenis laporan. Pengaduan darurat akan diprioritaskan.</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="font-bold text-lg text-gray-900 mb-2">• Bagaimana cara melacak?</h3>
            <p class="text-gray-700">Gunakan kode tracking yang diberikan setelah pengaduan dikirim. Kode tracking akan dikirim melalui WhatsApp.</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="font-bold text-lg text-gray-900 mb-2">• Bagaimana jika laporan saya tidak valid?</h3>
            <p class="text-gray-700">Tim akan memverifikasi setiap laporan. Jika tidak valid, Anda akan diberitahu melalui WhatsApp dengan alasan penolakan.</p>
        </div>
    </div>
</x-sections.section>

<!-- Category Selection Section -->
@if(isset($categories))
<x-sections.section spacing="py-12 md:py-16" background="bg-gray-50">
    <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-8">Kategori Pengaduan</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        @php
            $categoryIcons = [
                'infrastruktur' => 'M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-5.653a2.548 2.548 0 010-3.586L11.42 15.17z',
                'sampah' => 'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0',
                'air' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z',
                'listrik' => 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z',
                'keamanan' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
                'sosial' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.645-5.888-1.654A11.97 11.97 0 014.5 19.5m6.5-1.5a9.094 9.094 0 003.741.479 3 3 0 004.682-2.72m.94 3.198a11.944 11.944 0 01-5.888 1.654c-2.17 0-4.207-.645-5.888-1.654M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.645-5.888-1.654A11.97 11.97 0 014.5 19.5m6.5-1.5a9.094 9.094 0 003.741.479 3 3 0 004.682-2.72m.94 3.198a11.944 11.944 0 01-5.888 1.654c-2.17 0-4.207-.645-5.888-1.654M12 9a3 3 0 100-6 3 3 0 000 6z',
                'pendidikan' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443a55.381 55.381 0 015.25 2.882V15',
                'kesehatan' => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z',
                'lainnya' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
            ];
        @endphp
        @foreach($categories as $key => $label)
        <a href="{{ route('complaints.form') }}" 
           class="bg-white rounded-lg p-6 text-center shadow-md hover:shadow-lg transition-all border-2 border-transparent hover:border-emerald-500 block">
            <div class="w-12 h-12 mx-auto mb-3 text-emerald-600">
                <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $categoryIcons[$key] ?? $categoryIcons['lainnya'] }}"></path>
                </svg>
            </div>
            <div class="font-semibold text-gray-900 text-sm">{{ $label }}</div>
        </a>
        @endforeach
    </div>
</x-sections.section>
@endif

<!-- Final CTA Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-gradient-to-r from-emerald-500 to-teal-600">
    <div class="text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
            Siap Melaporkan Pengaduan?
        </h2>
        <p class="text-emerald-50 text-lg mb-8 max-w-2xl mx-auto">
            Klik tombol di bawah untuk mengisi formulir pengaduan. Prosesnya mudah dan cepat!
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('complaints.index') }}" 
               class="inline-flex items-center justify-center px-8 py-4 bg-white text-emerald-600 font-bold text-lg rounded-lg hover:bg-emerald-50 transition-colors duration-200 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Pengaduan Baru
            </a>
            <a href="{{ route('complaints.tracking-form') }}" 
               class="inline-flex items-center justify-center px-8 py-4 bg-emerald-700 text-white font-bold text-lg rounded-lg hover:bg-emerald-800 transition-colors duration-200 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Lacak Pengaduan
            </a>
        </div>
    </div>
</x-sections.section>
@endsection
