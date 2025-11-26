@extends('layouts.app')

@section('content')
<x-layouts.page-layout
    title="Lacak Pengaduan"
    description="Masukkan kode tracking untuk melihat status pengaduan Anda"
    page-header-gradient="from-blue-50 via-emerald-50 to-teal-50"
    :show-back-button="false"
    :show-bottom-back-button="true"
    :bottom-back-url="route('complaints.index')"
    bottom-back-text="Kembali"
    bottom-back-variant="outline">

<x-sections.section spacing="py-12 md:py-16 lg:py-20">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 border border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Cari Pengaduan</h2>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-red-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="#" method="GET" class="space-y-6" id="tracking-form">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Tracking <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" id="tracking-code" value="{{ request('code') }}" required
                           pattern="ADU-[A-Z0-9]{6}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-center text-lg font-mono tracking-wider uppercase"
                           placeholder="ADU-XXXXXX"
                           maxlength="10">
                    <p class="mt-2 text-xs text-gray-500">Format: ADU-XXXXXX (6 karakter alfanumerik)</p>
                </div>

                <button type="submit" 
                        class="w-full px-6 py-3 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
                    Lacak Pengaduan
                </button>
            </form>
            
            <script>
                document.getElementById('tracking-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const code = document.getElementById('tracking-code').value.trim().toUpperCase();
                    if (code.match(/^ADU-[A-Z0-9]{6}$/)) {
                        window.location.href = '{{ url("/pengaduan/track") }}/' + code;
                    } else {
                        alert('Format kode tracking tidak valid. Format: ADU-XXXXXX');
                    }
                });
            </script>

            <div class="mt-8 pt-8 border-t">
                <p class="text-sm text-gray-600 mb-4">Belum punya kode tracking?</p>
                <a href="{{ route('complaints.index') }}" 
                   class="inline-block text-emerald-600 hover:text-emerald-700 font-medium">
                    Buat Pengaduan Baru →
                </a>
            </div>
            </div>
        </div>
</x-sections.section>
</x-layouts.page-layout>
@endsection

