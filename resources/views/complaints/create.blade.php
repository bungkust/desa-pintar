@extends('layouts.app')

@section('content')
<x-sections.page-header 
    title="Pengaduan Masyarakat"
    description="Laporkan masalah atau keluhan Anda kepada pemerintah desa. Kami siap membantu menyelesaikan masalah Anda."
    gradient="from-emerald-50 via-teal-50 to-cyan-50"
/>

<!-- CTA Section - Large Buttons -->
<x-sections.section spacing="py-8 md:py-12" background="bg-white">
    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
        <a href="#form-pengaduan" 
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
    <div class="max-w-6xl mx-auto px-4">
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
    </div>
</x-sections.section>

<!-- Quick Help Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-emerald-50">
    <div class="max-w-6xl mx-auto px-4">
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
    </div>
</x-sections.section>

<!-- FAQ Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-8">Pertanyaan Umum</h2>
        <div class="space-y-4">
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
    </div>
</x-sections.section>

<!-- Category Selection Section -->
<x-sections.section spacing="py-12 md:py-16" background="bg-gray-50">
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-8">Kategori Pengaduan</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="category-grid">
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
            <button type="button" 
                    onclick="selectCategory('{{ $key }}')"
                    class="category-card bg-white rounded-lg p-6 text-center shadow-sm hover:shadow-md transition-all border-2 border-transparent hover:border-emerald-500 cursor-pointer">
                <div class="text-4xl mb-3">{{ $categoryIcons[$key] ?? '📄' }}</div>
                <div class="font-semibold text-gray-900 text-sm">{{ $label }}</div>
            </button>
            @endforeach
        </div>
    </div>
</x-sections.section>

<!-- Form Section -->
<x-sections.section spacing="py-12 md:py-16 lg:py-20" id="form-pengaduan">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Form Pengaduan</h2>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-red-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" id="complaint-form">
                @csrf
                
                <!-- Honeypot field -->
                <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">

                <!-- Selected Category Display -->
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg" id="selected-category-display" style="display: none;">
                    <p class="text-sm text-gray-600 mb-2">Kategori yang dipilih:</p>
                    <p class="font-semibold text-emerald-700" id="selected-category-text"></p>
                </div>

                <!-- Hidden category input -->
                <input type="hidden" name="category" id="category-input" required>

                <!-- Data Pelapor -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Pelapor</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama <span class="text-gray-500 text-xs">(opsional, direkomendasikan)</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="Masukkan nama Anda">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor WhatsApp <span class="text-red-500">*</span>
                            <span class="text-gray-500 text-xs">(wajib jika tidak anonim)</span>
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="08xxxxxxxxxx"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Format: 08xxxxxxxxxx</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                            <input type="text" name="rt" value="{{ old('rt') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="RT">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                            <input type="text" name="rw" value="{{ old('rw') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="RW">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="address" rows="2" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_anonymous" value="1" 
                                   {{ old('is_anonymous') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-sm text-gray-700">Laporkan sebagai anonim</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Jika dicentang, nama dan data pribadi Anda akan disembunyikan</p>
                    </div>
                </div>

                <!-- Informasi Pengaduan -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengaduan</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Pengaduan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="Contoh: Jalan rusak di RT 05">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" rows="5" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Jelaskan masalah atau keluhan Anda secara detail...">{{ old('description') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Maksimal 5000 karakter</p>
                    </div>
                </div>

                <!-- Lokasi -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lokasi</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Lokasi Pengaduan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="location_text" rows="2" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Contoh: Jl. Raya Donoharjo, RT 05, RW 02">{{ old('location_text') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Latitude (opsional)</label>
                            <input type="number" name="location_lat" value="{{ old('location_lat') }}" step="0.00000001"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="-7.xxxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Longitude (opsional)</label>
                            <input type="number" name="location_lng" value="{{ old('location_lng') }}" step="0.00000001"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="110.xxxxx">
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Koordinat GPS (opsional). Akan digunakan untuk deteksi duplikasi pengaduan.</p>
                </div>

                <!-- Upload Gambar -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Gambar</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Foto Pendukung (Maksimal 3 gambar, 2MB per gambar)
                        </label>
                        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               id="image-input">
                        <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WebP. Maksimal 2MB per gambar</p>
                    </div>

                    <div id="image-preview" class="grid grid-cols-3 gap-4 mt-4"></div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-6 border-t">
                    <a href="{{ route('complaints.tracking-form') }}" 
                       class="px-6 py-3 border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-50 font-medium rounded-lg transition-colors">
                        Lacak Pengaduan
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-emerald-600 text-white font-bold text-lg rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors shadow-md hover:shadow-lg">
                        Kirim Pengaduan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-sections.section>

@push('scripts')
<script src="{{ asset('js/complaint-form.js') }}"></script>
<script>
    function selectCategory(categoryKey) {
        // Set hidden input
        document.getElementById('category-input').value = categoryKey;
        
        // Update display
        const categoryText = document.querySelector(`[onclick="selectCategory('${categoryKey}')"]`).querySelector('.font-semibold').textContent;
        document.getElementById('selected-category-text').textContent = categoryText;
        document.getElementById('selected-category-display').style.display = 'block';
        
        // Update visual selection
        document.querySelectorAll('.category-card').forEach(card => {
            card.classList.remove('border-emerald-500', 'bg-emerald-50');
            card.classList.add('border-transparent');
        });
        const selectedCard = document.querySelector(`[onclick="selectCategory('${categoryKey}')"]`);
        selectedCard.classList.add('border-emerald-500', 'bg-emerald-50');
        selectedCard.classList.remove('border-transparent');
        
        // Scroll to form
        document.getElementById('form-pengaduan').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    // Set category if old input exists
    @if(old('category'))
        selectCategory('{{ old('category') }}');
    @endif
</script>
@endpush
@endsection
