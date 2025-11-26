@extends('layouts.app')

@section('content')
    <!-- Page Header -->
    <x-sections.page-header 
        title="Statistik Lengkap Desa {{ $settings->site_name ?? 'Donoharjo' }}"
        description="Data statistik lengkap dengan tren historis dan perbandingan tahun ke tahun untuk memberikan gambaran komprehensif tentang perkembangan desa."
        gradient="from-blue-50 via-emerald-50 to-teal-50"
    />

    @php
        // Icon mapping untuk statistik (Heroicons v2 outline)
        $iconMap = [
            'heroicon-o-users' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
            'heroicon-o-home' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
            'heroicon-o-map' => 'M9 20.25l-6.22-3.846a.75.75 0 01-.03-1.28l6.22-3.846a.75.75 0 01.78 0l6.22 3.846a.75.75 0 01-.03 1.28L9 20.25zm0 0l6.22 3.846a.75.75 0 00.78 0l6.22-3.846a.75.75 0 00-.03-1.28L15.78 12.5a.75.75 0 00-.78 0L9 16.346zm0 0l-6.22-3.846a.75.75 0 010-1.28L9 8.654l6.22 3.846a.75.75 0 010 1.28L9 20.25z',
            'heroicon-o-user-group' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.645-5.888-1.654A11.97 11.97 0 014.5 19.5m6.5-1.5a9.094 9.094 0 003.741.479 3 3 0 004.682-2.72m.94 3.198a11.944 11.944 0 01-5.888 1.654c-2.17 0-4.207-.645-5.888-1.654M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.645-5.888-1.654A11.97 11.97 0 014.5 19.5m6.5-1.5a9.094 9.094 0 003.741.479 3 3 0 004.682-2.72m.94 3.198a11.944 11.944 0 01-5.888 1.654c-2.17 0-4.207-.645-5.888-1.654M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.645-5.888-1.654A11.97 11.97 0 014.5 19.5m6.5-1.5a9.094 9.094 0 003.741.479 3 3 0 004.682-2.72m.94 3.198a11.944 11.944 0 01-5.888 1.654c-2.17 0-4.207-.645-5.888-1.654M12 9a3 3 0 100-6 3 3 0 000 6z',
            'heroicon-o-chart-bar' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
        ];
        $defaultIconPath = 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z';
        
        // Collect all statistics for summary cards
        $allStats = collect();
        if(isset($statistics) && $statistics->count() > 0) {
            foreach($statistics as $categoryStats) {
                $allStats = $allStats->merge($categoryStats);
            }
        }
    @endphp

    @if(isset($statistics) && $statistics->count() > 0)
    <!-- Ringkasan Statistik Section -->
    <x-sections.section title="Ringkasan Statistik">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                @foreach($allStats->take(6) as $stat)
                    @php
                        $iconPath = $stat->icon ? ($iconMap[$stat->icon] ?? $defaultIconPath) : $defaultIconPath;
                    @endphp
                    @include('components.cards.stat-card', [
                        'title' => $stat->label,
                        'value' => $stat->value,
                        'icon' => $iconPath,
                        'iconColor' => 'text-emerald-600',
                    ])
                @endforeach
            </div>
    </x-sections.section>

    <!-- Statistik Detail per Kategori -->
    <x-sections.section background="bg-gradient-to-br from-blue-50 via-emerald-50 to-teal-50">
        <div class="container mx-auto px-4 md:px-6 lg:px-8">
            @php
                $categoryLabels = [
                    'demografi' => 'Statistik Penduduk',
                    'geografis' => 'Statistik Wilayah',
                    'ekonomi' => 'Statistik Ekonomi',
                    'infrastruktur' => 'Statistik Infrastruktur',
                    'sosial' => 'Statistik Sosial',
                    'lainnya' => 'Statistik Lainnya',
                ];
            @endphp

            @foreach($statistics as $category => $categoryStats)
            @php
                $categoryName = $categoryLabels[$category] ?? 'Statistik ' . ucfirst($category);
            @endphp

            <!-- Category Section -->
            <div class="mb-10 md:mb-12">
                <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">{{ $categoryName }}</h3>
                
                <!-- Charts and Tables for each statistic -->
                @foreach($categoryStats as $stat)
                @if(isset($chartData) && isset($chartData[$stat->id]) && !empty($chartData[$stat->id]['years']) && count($chartData[$stat->id]['years']) > 0)
                
                <!-- Statistic Detail Card -->
                <div class="bg-white rounded-xl shadow-md p-6 md:p-8 border border-gray-100 mb-6 last:mb-0">
                    <h4 class="text-lg md:text-xl font-semibold text-gray-800 mb-6">{{ $stat->label }}</h4>
                    
                    <!-- Chart Section -->
                    <div class="mb-6">
                        <h5 class="text-sm font-medium text-gray-600 mb-3">Tren Per Tahun</h5>
                        <div class="relative h-64 md:h-80 bg-gray-50 rounded-lg p-4">
                            <canvas id="chart{{ $stat->id }}"></canvas>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-600 mb-3">Perbandingan Tahun ke Tahun</h5>
                        <div class="overflow-x-auto">
                            <table class="w-full border border-gray-200 rounded-lg text-sm">
                                <thead class="bg-gray-50 font-semibold">
                                    <tr>
                                        <th class="px-4 py-2 border-b text-left text-gray-700">Tahun</th>
                                        <th class="px-4 py-2 border-b text-left text-gray-700">Nilai</th>
                                        @if($stat->details && $stat->details->count() > 0 && $stat->details->first() && $stat->details->first()->additional_data)
                                        <th class="px-4 py-2 border-b text-left text-gray-700">Data Tambahan</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($stat->details && $stat->details->count() > 0)
                                    @foreach($stat->details->sortBy('year') as $index => $detail)
                                    <tr class="odd:bg-gray-50 even:bg-white">
                                        <td class="px-4 py-2 border-b text-gray-700 font-medium">{{ $detail->year }}</td>
                                        <td class="px-4 py-2 border-b text-gray-700">{{ $detail->value }}</td>
                                        @if($detail->additional_data)
                                        <td class="px-4 py-2 border-b text-gray-600">
                                            @foreach($detail->additional_data as $key => $value)
                                            <div class="text-xs"><span class="font-medium">{{ $key }}:</span> {{ $value }}</div>
                                            @endforeach
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="2" class="px-4 py-4 text-center text-gray-500 text-sm">Tidak ada data historis</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endforeach
        </div>
    </x-sections.section>

    @else
    <!-- Empty State -->
    <x-sections.section>
        <div class="text-center">
            <p class="text-gray-600 text-lg">Belum ada data statistik yang tersedia.</p>
        </div>
    </x-sections.section>
    @endif
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Chart.js Scripts -->
<script>
    function initCharts() {
        if (typeof Chart === 'undefined') {
            setTimeout(initCharts, 100);
            return;
        }

        @php
            $chartConfigs = [];
            if(isset($statistics) && isset($chartData)) {
                foreach($statistics as $category => $categoryStats) {
                    foreach($categoryStats as $stat) {
                        if(isset($chartData[$stat->id]) && count($chartData[$stat->id]['years']) > 0) {
                            $chartConfigs[] = [
                                'id' => $stat->id,
                                'label' => $stat->label,
                                'years' => $chartData[$stat->id]['years'],
                                'values' => $chartData[$stat->id]['values'],
                            ];
                        }
                    }
                }
            }
        @endphp

        const chartConfigs = @json($chartConfigs);
        
        // Color palette
        const colors = [
            { bg: 'rgba(16, 185, 129, 0.2)', border: 'rgba(16, 185, 129, 1)' }, // emerald
            { bg: 'rgba(59, 130, 246, 0.2)', border: 'rgba(59, 130, 246, 1)' }, // blue
            { bg: 'rgba(245, 158, 11, 0.2)', border: 'rgba(245, 158, 11, 1)' }, // amber
            { bg: 'rgba(139, 92, 246, 0.2)', border: 'rgba(139, 92, 246, 1)' }, // purple
        ];

        chartConfigs.forEach((config, index) => {
            const ctx = document.getElementById('chart' + config.id);
            if (!ctx) return;

            const color = colors[index % colors.length];

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: config.years,
                    datasets: [{
                        label: config.label,
                        data: config.values,
                        borderColor: color.border,
                        backgroundColor: color.bg,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: color.border,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        });
    }

    // Initialize charts when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharts);
    } else {
        initCharts();
    }
</script>
@endpush
