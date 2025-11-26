@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    $viewMode = $viewMode ?? 'card'; // Default to card
@endphp

@section('content')
    <x-layouts.page-layout
        title="Agenda Desa"
        description="Kegiatan dan acara terdekat di {{ $settings->site_name ?? 'Desa Donoharjo' }}"
        page-header-gradient="from-blue-50 via-emerald-50 to-teal-50">
        
        <x-sections.section spacing="py-12 md:py-16 lg:py-20">
        <!-- Filters and View Toggle -->
        <div class="bg-white rounded-lg shadow p-4 md:p-6 mb-8 text-gray-900">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <!-- View Toggle -->
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700">Tampilan:</span>
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        @php
                            $queryParams = request()->query();
                            $queryParams['view'] = 'card';
                            $cardUrl = route('agenda.index', $queryParams);
                            $queryParams['view'] = 'table';
                            $tableUrl = route('agenda.index', $queryParams);
                        @endphp
                        <a href="{{ $cardUrl }}" 
                           class="px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ $viewMode === 'card' ? 'bg-white text-emerald-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            <svg class="w-4 h-4 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            Card
                        </a>
                        <a href="{{ $tableUrl }}" 
                           class="px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ $viewMode === 'table' ? 'bg-white text-emerald-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            <svg class="w-4 h-4 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Table
                        </a>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('agenda.index') }}" class="space-y-4 md:space-y-0 md:grid md:grid-cols-3 md:gap-4">
                <!-- Hidden view mode -->
                <input type="hidden" name="view" value="{{ $viewMode }}">

                <!-- Search -->
                <div>
                    <label for="search" class="text-sm font-medium mb-1 block text-gray-700">Cari Agenda</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari judul agenda..."
                           class="border border-gray-300 rounded-lg w-full px-3 py-2 bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="text-sm font-medium mb-1 block text-gray-700">Kategori</label>
                    <select id="category" 
                            name="category" 
                            class="border border-gray-300 rounded-lg w-full px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label for="date" class="text-sm font-medium mb-1 block text-gray-700">Tanggal</label>
                    <input type="date" 
                           id="date" 
                           name="date" 
                           value="{{ request('date') }}"
                           class="border border-gray-300 rounded-lg w-full px-3 py-2 bg-white text-gray-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Submit Button -->
                <div class="md:flex md:items-end">
                    <button type="submit" 
                            class="w-full md:w-auto bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'category', 'date']))
                    <a href="{{ route('agenda.index', ['view' => $viewMode]) }}" 
                       class="w-full md:w-auto md:ml-2 mt-2 md:mt-0 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-center block">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        @if($agendas->count() > 0)
            @if($viewMode === 'card')
                <!-- Card View - Grid 3 Columns -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($agendas as $agenda)
                    <div class="bg-white shadow rounded-xl p-6 hover:shadow-lg transition-shadow duration-300 flex flex-col text-gray-900">
                        <!-- Category Badge -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-xs px-2 py-1 rounded bg-emerald-100 text-emerald-700 font-medium">
                                {{ $agenda->category_label }}
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="text-sm text-gray-500 mb-2">
                            {{ $agenda->date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                        </div>

                        <!-- Title -->
                        <h4 class="font-semibold text-lg mb-3 text-gray-900 line-clamp-2 flex-grow">
                            <a href="{{ route('agenda.show', $agenda->id) }}" class="hover:text-emerald-600 transition">
                                {{ $agenda->title }}
                            </a>
                        </h4>

                        <!-- Time -->
                        @if($agenda->start_time || $agenda->end_time)
                        <div class="flex items-center text-sm text-gray-600 mb-2">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @if($agenda->start_time && $agenda->end_time)
                                {{ $agenda->start_time->format('H:i') }} - {{ $agenda->end_time->format('H:i') }} WIB
                            @elseif($agenda->start_time)
                                {{ $agenda->start_time->format('H:i') }} WIB
                            @endif
                        </div>
                        @endif

                        <!-- Location -->
                        @if($agenda->location)
                        <div class="flex items-start text-sm text-gray-600 mb-4">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="line-clamp-2">{{ $agenda->location }}</span>
                        </div>
                        @endif

                        <!-- Description Preview -->
                        @if($agenda->description)
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                            {{ Str::limit(strip_tags($agenda->description), 100) }}
                        </p>
                        @endif

                        <!-- Link -->
                        <a href="{{ route('agenda.show', $agenda->id) }}" 
                           class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold text-sm transition mt-auto">
                            Lihat Detail
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Table View -->
                <div class="bg-white shadow rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($agendas as $agenda)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $agenda->date->locale('id')->isoFormat('D MMM YYYY') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $agenda->date->locale('id')->isoFormat('dddd') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <a href="{{ route('agenda.show', $agenda->id) }}" class="hover:text-emerald-600 transition">
                                                        {{ $agenda->title }}
                                                    </a>
                                                </div>
                                                @if($agenda->description)
                                                <div class="text-xs text-gray-500 mt-1 line-clamp-1">
                                                    {{ Str::limit(strip_tags($agenda->description), 80) }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($agenda->start_time || $agenda->end_time)
                                        <div class="text-sm text-gray-900">
                                            @if($agenda->start_time && $agenda->end_time)
                                                {{ $agenda->start_time->format('H:i') }} - {{ $agenda->end_time->format('H:i') }}
                                            @elseif($agenda->start_time)
                                                {{ $agenda->start_time->format('H:i') }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">WIB</div>
                                        @else
                                        <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs">
                                            {{ $agenda->location ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            {{ $agenda->category_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('agenda.show', $agenda->id) }}" 
                                           class="text-emerald-600 hover:text-emerald-900 transition">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
        <div class="text-center py-12">
            <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-gray-600 text-lg">Tidak ada agenda yang ditemukan.</p>
        </div>
        @endif
        </x-sections.section>
    </x-layouts.page-layout>
@endsection
