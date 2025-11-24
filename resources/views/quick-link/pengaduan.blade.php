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
        <a href="{{ route('complaints.create') }}" 
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
                <span class="text-2xl mr-3">🛣️</span>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Jalan Rusak / Berlubang</h3>
                    <p class="text-sm text-gray-600">Laporkan kondisi jalan yang rusak atau berlubang</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-start">
                <span class="text-2xl mr-3">💡</span>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Lampu Jalan Mati</h3>
                    <p class="text-sm text-gray-600">Laporkan lampu jalan yang tidak menyala</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-start">
                <span class="text-2xl mr-3">🗑️</span>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Sampah Menumpuk</h3>
                    <p class="text-sm text-gray-600">Laporkan tumpukan sampah yang mengganggu</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-start">
                <span class="text-2xl mr-3">🛡️</span>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Gangguan Keamanan</h3>
                    <p class="text-sm text-gray-600">Laporkan gangguan keamanan di lingkungan</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-start">
                <span class="text-2xl mr-3">💧</span>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Pipa Bocor</h3>
                    <p class="text-sm text-gray-600">Laporkan pipa air yang bocor atau rusak</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-start">
                <span class="text-2xl mr-3">🌳</span>
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
                'infrastruktur' => '🛠️',
                'sampah' => '🗑️',
                'air' => '💧',
                'listrik' => '⚡',
                'keamanan' => '🛡️',
                'sosial' => '🤝',
                'pendidikan' => '🎓',
                'kesehatan' => '❤️',
                'lainnya' => '📄',
            ];
        @endphp
        @foreach($categories as $key => $label)
        <a href="{{ route('complaints.create') }}#form-pengaduan" 
           class="bg-white rounded-lg p-6 text-center shadow-md hover:shadow-lg transition-all border-2 border-transparent hover:border-emerald-500 block">
            <div class="text-4xl mb-3">{{ $categoryIcons[$key] ?? '📄' }}</div>
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
            <a href="{{ route('complaints.create') }}" 
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
