@extends('layouts.app')

@section('content')
    @if($year === null || $summary === null)
        <!-- Empty State -->
        <x-sections.section>
            @include('components.empty-state', [
                'title' => 'Data APBDes Belum Tersedia',
                'message' => empty($availableYears) ? 'Belum ada data APBDes yang tersedia saat ini.' : 'Data APBDes untuk tahun yang diminta belum tersedia.',
                'action' => !empty($availableYears) ? '<a href="' . route('apbdes.show', $availableYears[0]) . '" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors">Lihat Data Tahun ' . $availableYears[0] . '</a>' : null,
            ])
        </x-sections.section>
    @else
        <!-- Header Section -->
        <x-sections.page-header 
            title="APBDes {{ $year }} - {{ $settings->site_name ?? 'Desa Donoharjo' }}"
            description="Laporan realisasi anggaran desa secara lengkap, sebagai bentuk pertanggungjawaban kepada masyarakat. <strong>Realisasi</strong> adalah dana yang benar-benar telah digunakan, <strong>Anggaran</strong> adalah rencana pengeluaran yang ditetapkan, dan <strong>Persentase</strong> menunjukkan seberapa besar realisasi dibandingkan dengan anggaran yang direncanakan."
            gradient="from-blue-50 via-emerald-50 to-teal-50"
        />
        
        @if(count($availableYears) > 1)
        <section class="py-0 -mt-8">
            <div class="container mx-auto px-4 md:px-6 lg:px-8">
                <div class="text-center">
                    @include('components.selects.year-selector', [
                        'currentYear' => $year,
                        'availableYears' => $availableYears,
                        'routeName' => 'apbdes.show',
                    ])
                </div>
            </div>
        </section>
        @endif

        <!-- Chart Data (hidden, for JavaScript) -->
        @if($chartData)
        <script id="chart-data" type="application/json">@json($chartData)</script>
        @endif

        @push('scripts')
        @vite('resources/js/charts.js')
        @endpush

        <!-- Ringkasan Utama Section -->
        <x-sections.section title="Pelaksanaan (Ringkasan Utama)">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                @include('components.cards.apbdes-summary-card', [
                    'title' => 'Pendapatan',
                    'realisasi' => $summary['pendapatan']['realisasi'] ?? 0,
                    'anggaran' => $summary['pendapatan']['anggaran'] ?? 0,
                    'percentage' => $summary['pendapatan']['percentage'] ?? 0,
                ])
                
                @include('components.cards.apbdes-summary-card', [
                    'title' => 'Belanja',
                    'realisasi' => $summary['belanja']['realisasi'] ?? 0,
                    'anggaran' => $summary['belanja']['anggaran'] ?? 0,
                    'percentage' => $summary['belanja']['percentage'] ?? 0,
                ])
                
                @include('components.cards.apbdes-summary-card', [
                    'title' => 'Pembiayaan',
                    'realisasi' => $summary['pembiayaan']['realisasi'] ?? 0,
                    'anggaran' => $summary['pembiayaan']['anggaran'] ?? 0,
                    'percentage' => $summary['pembiayaan']['percentage'] ?? 0,
                ])
            </div>
        </x-sections.section>

        <!-- Bar Chart Section -->
        @if($chartData && isset($chartData['barChartComparison']))
        <x-sections.section>
            @include('components.charts.chart-container', [
                'title' => 'Perbandingan Realisasi vs Anggaran',
                'subtitle' => 'Grafik perbandingan antara rencana anggaran dan realisasi sepanjang tahun berjalan untuk Pendapatan, Belanja, dan Pembiayaan.',
                'chartId' => 'comparison-bar-chart',
            ])
        </x-sections.section>
        @endif

        <!-- Pendapatan Section -->
        <x-sections.section title="Pendapatan (Rincian Detail per Sumber)">
            <div class="space-y-8">
                @if($chartData && isset($chartData['pieChartPendapatan']) && count($chartData['pieChartPendapatan']['labels']) > 0 && count($revenueDetails) > 0)
                @include('components.charts.chart-with-list', [
                    'title' => 'Distribusi Pendapatan',
                    'subtitle' => 'Proporsi pendapatan dari berbagai sumber yang masuk ke kas desa.',
                    'chartId' => 'pendapatan-pie-chart',
                    'height' => 'h-48 md:h-56',
                    'items' => $revenueDetails,
                    'listTitle' => 'Sumber Pendapatan',
                    'listColor' => 'text-emerald-600',
                ])
                @endif

                @if(count($revenueDetails) > 0)
                <x-tables.data-table-card 
                    title="Tabel Rincian Pendapatan"
                    subtitle="Detail lengkap realisasi, anggaran, dan persentase capaian untuk setiap sumber pendapatan.">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                    <thead class="bg-emerald-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Sumber Pendapatan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Realisasi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Anggaran</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($revenueDetails as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['category'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                Rp {{ number_format($item['realisasi'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-700">
                                Rp {{ number_format($item['anggaran'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <div class="flex-1 max-w-[100px]">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full transition-all duration-500" 
                                                 style="width: {{ min(100, max(0, $item['percentage'])) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 min-w-[70px] text-right">
                                        {{ number_format($item['percentage'], 2, ',', '.') }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </x-tables.data-table-card>
                @else
                <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-500">
                    Belum ada data pendapatan untuk tahun ini.
                </div>
                @endif
            </div>
        </x-sections.section>

        <!-- Belanja Section -->
        <x-sections.section title="Pembelanjaan (Rincian per Bidang)">
            <div class="space-y-8">
                @if($chartData && isset($chartData['pieChartBelanja']) && count($chartData['pieChartBelanja']['labels']) > 0 && count($expenseDetails) > 0)
                @include('components.charts.chart-with-list', [
                    'title' => 'Distribusi Belanja',
                    'subtitle' => 'Proporsi belanja per bidang yang menunjukkan alokasi anggaran desa.',
                    'chartId' => 'belanja-pie-chart',
                    'height' => 'h-48 md:h-56',
                    'items' => $expenseDetails,
                    'listTitle' => 'Bidang Belanja',
                    'listColor' => 'text-red-600',
                ])
                @endif

                @if(count($expenseDetails) > 0)
                <x-tables.data-table-card 
                    title="Tabel Rincian Belanja"
                    subtitle="Detail lengkap realisasi, anggaran, dan persentase capaian untuk setiap bidang belanja.">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                    <thead class="bg-emerald-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Bidang Belanja</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Realisasi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Anggaran</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($expenseDetails as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item['category'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                Rp {{ number_format($item['realisasi'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-700">
                                Rp {{ number_format($item['anggaran'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <div class="flex-1 max-w-[100px]">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-red-500 h-2 rounded-full transition-all duration-500" 
                                                 style="width: {{ min(100, max(0, $item['percentage'])) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 min-w-[70px] text-right">
                                        {{ number_format($item['percentage'], 2, ',', '.') }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </x-tables.data-table-card>
                @else
                <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-500">
                    Belum ada data belanja untuk tahun ini.
                </div>
                @endif
            </div>
        </x-sections.section>

        <!-- Pembiayaan Section -->
        @if(count($pembiayaanDetails ?? []) > 0)
        <x-sections.section title="Pembiayaan (Rincian Detail)">
            <div>
                <x-tables.data-table-card 
                    title="Tabel Rincian Pembiayaan"
                    subtitle="Detail lengkap realisasi, anggaran, dan persentase capaian untuk setiap kategori pembiayaan.">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                    <thead class="bg-emerald-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Kategori Pembiayaan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Realisasi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Anggaran</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider border-b">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pembiayaanDetails as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item['category'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                Rp {{ number_format($item['realisasi'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-700">
                                Rp {{ number_format($item['anggaran'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <div class="flex-1 max-w-[100px]">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" 
                                                 style="width: {{ min(100, max(0, $item['percentage'])) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 min-w-[70px] text-right">
                                        {{ number_format($item['percentage'], 2, ',', '.') }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </x-tables.data-table-card>
            </div>
        </x-sections.section>
        @endif

        <!-- CTA Section -->
        <x-sections.section>
            <div class="text-center">
                @include('components.buttons.back-button', [
                    'href' => '/#transparansi',
                    'label' => '← Kembali ke Transparansi',
                ])
            </div>
        </x-sections.section>
    @endif
@endsection