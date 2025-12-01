@extends('layouts.app')

@section('content')
<x-layouts.page-layout
    title="Form Pengaduan Masyarakat"
    description="Isi form berikut dengan lengkap dan benar"
    page-header-gradient="from-blue-50 via-emerald-50 to-teal-50"
    :show-back-button="false">

<x-sections.section spacing="py-12 md:py-16" background="bg-gray-50">
        <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h3 class="font-semibold text-red-800 mb-2">Terjadi kesalahan:</h3>
                    <ul class="list-disc list-inside text-red-700">
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
                <input type="hidden" name="category" id="category-input" value="{{ old('category', request('category')) }}" required>

                <!-- Fieldset 1: Data Pelapor -->
                <fieldset class="mb-8 pb-8">
                    <legend class="text-xl font-bold text-gray-900 mb-6">Data Pelapor</legend>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama <span class="text-gray-500 text-xs">(opsional, direkomendasikan)</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="Masukkan nama Anda">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor WhatsApp <span class="text-red-500">*</span>
                            <span class="text-gray-500 text-xs">(wajib jika tidak anonim)</span>
                        </label>
                        <input type="tel"
                               name="phone"
                               value="{{ old('phone') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="08xxxxxxxxxx"
                               id="phone-input"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               maxlength="15"
                               autocomplete="tel"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <p class="mt-1 text-xs text-gray-500">Format: 08xxxxxxxxxx (angka saja)</p>
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                            <input type="text"
                                   name="rt"
                                   value="{{ old('rt') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="RT"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   maxlength="3"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('rt')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                            <input type="text"
                                   name="rw"
                                   value="{{ old('rw') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="RW"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   maxlength="3"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('rw')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="address"
                                  rows="2"
                                  maxlength="500"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Maksimal 500 karakter.</p>
                        @error('address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_anonymous" value="1" 
                                   {{ old('is_anonymous') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                   id="anonymous-checkbox">
                            <span class="ml-2 text-sm text-gray-700">Laporkan sebagai anonim</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">Jika dicentang, nama dan data pribadi Anda akan disembunyikan</p>
                    </div>
                </fieldset>

                <!-- Fieldset 2: Detail Pengaduan -->
                <fieldset class="mb-8 pb-8">
                    <legend class="text-xl font-bold text-gray-900 mb-6">Detail Pengaduan</legend>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="category" id="category-select" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories ?? [] as $key => $label)
                                <option value="{{ $key }}" {{ old('category', request('category')) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Pengaduan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               value="{{ old('title') }}"
                               required
                               maxlength="255"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="Contoh: Jalan rusak di RT 05">
                        <p class="mt-1 text-xs text-gray-500">Maksimal 255 karakter.</p>
                        @error('title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" rows="5" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Jelaskan masalah atau keluhan Anda secara detail...">{{ old('description') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Maksimal 5000 karakter</p>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <!-- Fieldset 3: Lokasi -->
                <fieldset class="mb-8 pb-8">
                    <legend class="text-xl font-bold text-gray-900 mb-6">Lokasi</legend>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Lokasi Pengaduan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="location_text" rows="2" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Contoh: Jl. Raya Donoharjo, RT 05, RW 02">{{ old('location_text') }}</textarea>
                        @error('location_text')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Latitude (opsional)</label>
                            <input type="number" name="location_lat" value="{{ old('location_lat') }}" step="0.00000001"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="-7.xxxxx">
                            @error('location_lat')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Longitude (opsional)</label>
                            <input type="number" name="location_lng" value="{{ old('location_lng') }}" step="0.00000001"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="110.xxxxx">
                            @error('location_lng')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Koordinat GPS (opsional). Akan digunakan untuk deteksi duplikasi pengaduan.</p>
                </fieldset>

                <!-- Fieldset 4: Upload Foto -->
                <fieldset class="mb-8">
                    <legend class="text-xl font-bold text-gray-900 mb-6">Upload Foto</legend>
                    
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
                </fieldset>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-6 border-t">
                    <a href="{{ route('complaints.index') }}" 
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 hover:bg-gray-50 font-medium rounded-lg transition-colors">
                        Batal
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
</x-layouts.page-layout>

@push('scripts')
<script src="{{ asset('js/complaint-form.js') }}"></script>
<script>
    // Handle anonymous checkbox
    document.getElementById('anonymous-checkbox')?.addEventListener('change', function() {
        const phoneInput = document.getElementById('phone-input');
        if (this.checked) {
            phoneInput.removeAttribute('required');
        } else {
            phoneInput.setAttribute('required', 'required');
        }
    });

    // Handle category select change
    document.getElementById('category-select')?.addEventListener('change', function() {
        document.getElementById('category-input').value = this.value;
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('selected-category-text').textContent = selectedOption.text;
            document.getElementById('selected-category-display').style.display = 'block';
        } else {
            document.getElementById('selected-category-display').style.display = 'none';
        }
    });

    // Set category from URL parameter
    @if(request('category'))
        document.getElementById('category-select').value = '{{ request('category') }}';
        document.getElementById('category-select').dispatchEvent(new Event('change'));
    @endif

    // Initialize anonymous checkbox state
    document.getElementById('anonymous-checkbox')?.dispatchEvent(new Event('change'));
</script>
@endpush
@endsection


