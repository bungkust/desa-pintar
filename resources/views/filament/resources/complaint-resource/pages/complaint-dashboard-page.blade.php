<x-filament-panels::page>
    @php
        $stats = $this->stats;
        $byCategory = $this->byCategory;
        $byRT = $this->byRT;
        $topIssues = $this->topIssues;
    @endphp

    <div class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Total Bulan Ini</div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total_this_month'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Selesai</div>
                <div class="text-2xl font-bold text-emerald-600">{{ $stats['completed'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Pending</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Overdue</div>
                <div class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Nearing Deadline</div>
                <div class="text-2xl font-bold text-orange-600">{{ $stats['nearing_deadline'] }}</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- By Category -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaduan Berdasarkan Kategori</h3>
                <div class="space-y-2">
                    @foreach($byCategory as $category => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">{{ ucfirst($category) }}</span>
                            <div class="flex items-center gap-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ ($count / max($byCategory->values()->toArray())) * 100 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 w-8 text-right">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- By RT -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 RT dengan Pengaduan Terbanyak</h3>
                <div class="space-y-2">
                    @foreach($byRT as $rt => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">RT {{ $rt }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $count }} pengaduan</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Issues -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Masalah yang Paling Sering Dilaporkan</h3>
            <div class="space-y-3">
                @foreach($topIssues as $issue)
                    <div class="flex items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-700 flex-1">{{ $issue->title }}</span>
                        <span class="text-sm font-medium text-gray-900 ml-4">{{ $issue->count }} kali</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
