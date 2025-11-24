@extends('layouts.app')

@section('content')
    <x-sections.page-header 
        title="Peraturan Desa"
        description="Daftar peraturan desa yang berlaku di {{ $settings->site_name ?? 'Desa Donoharjo' }}"
        gradient="from-blue-50 via-emerald-50 to-teal-50"
    />

    <x-sections.section spacing="py-12 md:py-16 lg:py-20">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Nomor</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Tentang</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Tanggal</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($peraturanDesa as $index => $peraturan)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $peraturan['nomor'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $peraturan['tentang'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $peraturan['tanggal'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        {{ $peraturan['status'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Informasi:</strong> Untuk mendapatkan salinan lengkap peraturan desa, silakan menghubungi kantor desa atau mengunduh melalui sistem informasi desa.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </x-sections.section>
@endsection

