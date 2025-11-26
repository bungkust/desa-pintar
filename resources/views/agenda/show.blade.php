@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <x-sections.page-header 
        title="{{ $agenda->title }}"
        description="Detail agenda kegiatan desa"
        gradient="from-blue-50 via-emerald-50 to-teal-50"
    />

    <x-sections.section spacing="py-12 md:py-16 lg:py-20">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white shadow rounded-xl p-6 md:p-8 text-gray-900">
                <!-- Header Info -->
                <div class="mb-6 pb-6 border-b">
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <div class="text-xs px-2 py-1 rounded bg-emerald-100 text-emerald-700">
                            {{ $agenda->category_label }}
                        </div>
                        @if($agenda->is_recurring)
                        <div class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700">
                            Berulang: {{ $agenda->recurring_type == 'weekly' ? 'Mingguan' : 'Bulanan' }}
                        </div>
                        @endif
                    </div>

                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        {{ $agenda->title }}
                    </h1>

                    <!-- Date & Time -->
                    <div class="space-y-2 text-gray-700">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-medium">{{ $agenda->date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                        </div>

                        @if($agenda->start_time || $agenda->end_time)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">
                                @if($agenda->start_time && $agenda->end_time)
                                    {{ $agenda->start_time->format('H:i') }} - {{ $agenda->end_time->format('H:i') }} WIB
                                @elseif($agenda->start_time)
                                    {{ $agenda->start_time->format('H:i') }} WIB
                                @endif
                            </span>
                        </div>
                        @endif

                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-2 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium">{{ $agenda->location }}</span>
                        </div>

                        @if($agenda->organizer)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>Organizer: <span class="font-medium">{{ $agenda->organizer }}</span></span>
                        </div>
                        @endif

                        @if($agenda->contact_person)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>Kontak: <span class="font-medium">{{ $agenda->contact_person }}</span></span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Image -->
                @if($agenda->image)
                <div class="mb-6">
                    <img src="{{ Storage::url($agenda->image) }}" 
                         alt="{{ $agenda->title }}" 
                         class="w-full rounded-lg shadow-md"
                         loading="lazy"
                         decoding="async">
                </div>
                @endif

                <!-- Description -->
                @if($agenda->description)
                <div class="mb-6 prose prose-lg max-w-none prose-neutral text-gray-900 prose-p:text-gray-700 prose-headings:text-gray-900">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900">Deskripsi</h2>
                    <div class="text-gray-700 whitespace-pre-line">
                        {!! nl2br(e($agenda->description)) !!}
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-wrap gap-4 pt-6 border-t">
                    @if($agenda->google_maps_url)
                    <a href="{{ $agenda->google_maps_url }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Buka di Google Maps
                    </a>
                    @endif

                    <!-- Google Calendar Link -->
                    @php
                        $startDateTime = $agenda->date->format('Ymd');
                        if ($agenda->start_time) {
                            $startDateTime .= 'T' . str_replace(':', '', $agenda->start_time->format('His'));
                        } else {
                            $startDateTime .= 'T000000';
                        }

                        $endDateTime = $agenda->date->format('Ymd');
                        if ($agenda->end_time) {
                            $endDateTime .= 'T' . str_replace(':', '', $agenda->end_time->format('His'));
                        } else {
                            $endDateTime .= 'T235959';
                        }

                        $googleCalendarUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE';
                        $googleCalendarUrl .= '&text=' . urlencode($agenda->title);
                        $googleCalendarUrl .= '&dates=' . $startDateTime . '/' . $endDateTime;
                        $googleCalendarUrl .= '&details=' . urlencode($agenda->description ?? '');
                        $googleCalendarUrl .= '&location=' . urlencode($agenda->location);
                    @endphp
                    <a href="{{ $googleCalendarUrl }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Tambahkan ke Google Calendar
                    </a>

                    <a href="{{ route('agenda.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Daftar Agenda
                    </a>
                </div>
            </div>
        </div>
    </x-sections.section>
@endsection

