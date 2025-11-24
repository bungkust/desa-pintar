@php
    use App\Models\Agenda;
    use App\Settings\GeneralSettings;
    use Illuminate\Support\Facades\Cache;
    
    $upcomingAgendas = Cache::remember('home_upcoming_agendas', 1800, function () {
        return Agenda::upcoming()
            ->limit(3)
            ->get();
    });
    
    // Get settings if not provided
    if (!isset($settings)) {
        $settings = Cache::rememberForever('general_settings', function () {
            try {
                return app(GeneralSettings::class);
            } catch (\Exception $e) {
                return (object) [
                    'site_name' => 'Desa Donoharjo',
                ];
            }
        });
    }
@endphp

@if($upcomingAgendas && $upcomingAgendas->count() > 0)
<section id="agenda" class="py-12 md:py-16 lg:py-20 bg-gradient-to-br from-blue-50 via-emerald-50 to-teal-50">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-10 md:mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-gray-900">
                Agenda Desa
            </h2>
            <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">
                Kegiatan dan acara terdekat di {{ $settings->site_name ?? 'Desa Donoharjo' }}
            </p>
        </div>

        <!-- Agenda Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-6 lg:gap-8">
            @foreach($upcomingAgendas as $agenda)
            <div class="bg-white shadow rounded-xl p-6 hover:shadow-lg transition-shadow duration-300">
                <!-- Date Badge -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <div class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700 font-semibold">
                            {{ $agenda->date->locale('id')->isoFormat('D MMM YYYY') }}
                        </div>
                        @if($agenda->is_featured)
                        <div class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-700 font-semibold">
                            Featured
                        </div>
                        @endif
                    </div>
                    <div class="text-xs px-2 py-1 rounded bg-emerald-100 text-emerald-700">
                        {{ $agenda->category_label }}
                    </div>
                </div>

                <!-- Title -->
                <h3 class="font-semibold text-lg mb-3 text-gray-900 line-clamp-2">
                    <a href="{{ route('agenda.show', $agenda->id) }}" class="hover:text-emerald-600 transition">
                        {{ $agenda->title }}
                    </a>
                </h3>

                <!-- Time Range -->
                @if($agenda->start_time || $agenda->end_time)
                <div class="flex items-center text-sm text-gray-600 mb-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="flex items-start text-sm text-gray-600 mb-4">
                    <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="line-clamp-2">{{ $agenda->location }}</span>
                </div>

                <!-- Link to Detail -->
                <a href="{{ route('agenda.show', $agenda->id) }}" 
                   class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold text-sm transition">
                    Lihat Detail
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            @endforeach
        </div>

        <!-- View All Button -->
        <div class="text-center mt-8 md:mt-10 lg:mt-12">
            <a href="{{ route('agenda.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                Lihat Semua Agenda
            </a>
        </div>
    </div>
</section>
@endif

