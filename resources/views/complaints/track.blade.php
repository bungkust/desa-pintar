@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    
    $statusColors = [
        'backlog' => 'bg-gray-100 text-gray-800 border-gray-300',
        'verification' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'todo' => 'bg-blue-100 text-blue-800 border-blue-300',
        'in_progress' => 'bg-blue-100 text-blue-800 border-blue-300',
        'done' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
        'rejected' => 'bg-red-100 text-red-800 border-red-300',
    ];
    $statusLabels = [
        'backlog' => 'Backlog',
        'verification' => 'Verifikasi',
        'todo' => 'Proses',
        'in_progress' => 'Proses',
        'done' => 'Selesai',
        'rejected' => 'Ditolak',
    ];
    
    // Calculate SLA days remaining (rounded)
    $slaDaysRemaining = null;
    $slaStatus = 'normal';
    if ($complaint->sla_deadline) {
        $daysDiff = now()->diffInDays($complaint->sla_deadline, false);
        if ($daysDiff < 0) {
            $slaDaysRemaining = (int) ceil(abs($daysDiff));
            $slaStatus = 'overdue';
        } elseif ($daysDiff <= 3) {
            $slaDaysRemaining = (int) ceil($daysDiff);
            $slaStatus = 'nearing';
        } else {
            $slaDaysRemaining = (int) ceil($daysDiff);
            $slaStatus = 'normal';
        }
    }
    
    // Get last update date
    $lastUpdate = $updates->sortByDesc('created_at')->first();
    $lastUpdateDate = $lastUpdate ? $lastUpdate->created_at : $complaint->created_at;
    
    // Define all possible statuses for timeline (no icons)
    $allStatuses = [
        'backlog' => ['label' => 'Kirim Laporan'],
        'verification' => ['label' => 'Verifikasi Admin'],
        'todo' => ['label' => 'Dalam Proses'],
        'in_progress' => ['label' => 'Dalam Proses'],
        'done' => ['label' => 'Selesai'],
        'rejected' => ['label' => 'Ditolak'],
    ];
    
    // Build timeline - get updates in chronological order (oldest first)
    $updatesChronological = $updates->sortBy('created_at')->values();
    
    // Determine which statuses have been completed (avoid duplicates)
    $completedStatuses = ['backlog']; // Always completed
    $statusUpdateMap = [];
    
    foreach ($updatesChronological as $update) {
        if (!in_array($update->status_to, $completedStatuses)) {
            $completedStatuses[] = $update->status_to;
        }
        // Use the first occurrence of each status
        if (!isset($statusUpdateMap[$update->status_to])) {
            $statusUpdateMap[$update->status_to] = $update;
        }
    }
    
    // Build full timeline showing all statuses in order (no duplicates)
    $timelineStatuses = [];
    $statusOrder = ['backlog', 'verification', 'todo', 'in_progress', 'done', 'rejected'];
    
    foreach ($statusOrder as $status) {
        // Skip rejected if status is done, and skip done if status is rejected
        if (($status === 'done' && $complaint->status === 'rejected') || 
            ($status === 'rejected' && $complaint->status === 'done')) {
            continue;
        }
        
        // Handle todo/in_progress - only show one
        if ($status === 'todo' && in_array('in_progress', $completedStatuses)) {
            continue; // Skip todo if already in_progress
        }
        if ($status === 'in_progress' && in_array('todo', $completedStatuses) && !in_array('in_progress', $completedStatuses)) {
            // If todo exists but not in_progress, show todo instead
            continue;
        }
        
        $isCompleted = in_array($status, $completedStatuses);
        $isCurrent = $complaint->status === $status;
        $update = $statusUpdateMap[$status] ?? null;
        
        $timelineStatuses[] = [
            'status' => $status,
            'completed' => $isCompleted,
            'isCurrent' => $isCurrent,
            'date' => $isCompleted ? ($update ? $update->created_at : ($status === 'backlog' ? $complaint->created_at : null)) : null,
            'note' => $update ? $update->note : ($status === 'backlog' ? 'Pengaduan diterima dan sedang dalam antrian.' : null),
            'updatedBy' => $update && $update->updatedBy ? $update->updatedBy->name : null,
            'image' => $update ? $update->image : null,
        ];
        
        // Stop after current status (don't show future statuses if done/rejected)
        if (in_array($complaint->status, ['done', 'rejected']) && $isCurrent) {
            break;
        }
    }
@endphp

@section('content')
<x-sections.page-header 
    title="Lacak Pengaduan"
    description="Pantau status pengaduan Anda secara real-time"
    gradient="from-blue-50 via-emerald-50 to-teal-50"
/>

<x-sections.section spacing="py-12 md:py-16 lg:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-6">
            <!-- Back Button -->
            <div>
                @include('components.buttons.back-button', [
                    'href' => route('complaints.index'),
                    'label' => 'Kembali ke Halaman Pengaduan',
                    'variant' => 'gray',
                ])
            </div>

            <!-- Divider -->
            <hr class="border-gray-200">

            <!-- 2️⃣ Premium Tracking Code Card -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl border-2 border-blue-500 p-6 shadow-lg" style="background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-white mb-2 uppercase tracking-wide" style="text-shadow: 0 1px 2px rgba(0,0,0,0.2);">Kode Tracking</p>
                        <div class="flex items-center gap-4 flex-wrap">
                            <span class="text-3xl md:text-4xl font-bold text-white font-mono tracking-wider bg-white/25 px-4 py-2 rounded-lg border-2 border-white/40 backdrop-blur-sm" style="text-shadow: 0 2px 4px rgba(0,0,0,0.4); background-color: rgba(255,255,255,0.25);">
                                {{ $complaint->tracking_code }}
                            </span>
                            <button 
                                id="copyTrackingCode"
                                class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2"
                                data-code="{{ $complaint->tracking_code }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span id="copyText">Salin</span>
                            </button>
                        </div>
                        <p class="text-sm text-white mt-3 flex items-center gap-2" style="text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Dibuat: {{ $complaint->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <hr class="border-gray-200">

            <!-- 4️⃣ Detail Pengaduan in Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Detail Pengaduan</h3>
                    </div>
                </div>
                
                <!-- Content Section with Grid Layout -->
                <div class="p-6">
                    <div class="space-y-0">
                        <!-- Kategori -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 py-4 border-b border-gray-100">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Kategori</p>
                                    <p class="text-base font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $complaint->category)) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Judul -->
                        <div class="py-4 border-b border-gray-100">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 w-full">
                                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Judul Pengaduan</p>
                                    <p class="text-base font-semibold text-gray-900 leading-relaxed w-full">{{ $complaint->title }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Deskripsi -->
                        <div class="py-4 border-b border-gray-100">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 w-full">
                                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Deskripsi</p>
                                    <p class="text-base text-gray-900 whitespace-pre-wrap leading-relaxed w-full">{{ $complaint->description }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Lokasi & RT/RW -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-4 border-b border-gray-100">
                            <!-- Lokasi -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Lokasi</p>
                                    <p class="text-base font-medium text-gray-900">{{ $complaint->location_text }}</p>
                                </div>
                    </div>
                    
                            <!-- RT/RW -->
                    @if($complaint->rt || $complaint->rw)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">RT/RW</p>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($complaint->rt)
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-100 text-indigo-800 text-sm font-semibold border border-indigo-200">
                                                RT {{ $complaint->rt }}
                                            </span>
                                        @endif
                                        @if($complaint->rw)
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-100 text-indigo-800 text-sm font-semibold border border-indigo-200">
                                                RW {{ $complaint->rw }}
                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Foto Pendukung -->
                    @if($complaint->images && count($complaint->images) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 py-4">
                            <div class="flex items-start gap-3 md:col-span-3">
                                <div class="w-8 h-8 rounded-lg bg-pink-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Foto Pendukung</p>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($complaint->images as $image)
                                            <a href="{{ Storage::url($image) }}" target="_blank" class="block group relative">
                                                <div class="aspect-video rounded-lg overflow-hidden border-2 border-gray-200 group-hover:border-blue-400 transition-colors shadow-sm group-hover:shadow-md">
                                        <img src="{{ Storage::url($image) }}" alt="Foto pengaduan" 
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                </div>
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity rounded-lg flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                    </svg>
                                                </div>
                                    </a>
                                @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <hr class="border-gray-200">

            <!-- 5️⃣ Improved Timeline with Visual Stepper -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6 shadow-sm">
                <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-200">Timeline Status</h3>
                
                <div class="relative pl-2">
                    @foreach($timelineStatuses as $index => $timelineItem)
                        <div class="flex gap-4 pb-8 last:pb-0 relative">
                            <!-- Timeline Line & Dot -->
                            <div class="flex flex-col items-center flex-shrink-0">
                                @if($timelineItem['completed'])
                                    <!-- Completed (green circle with checkmark) -->
                                    <div class="w-5 h-5 rounded-full bg-emerald-600 border-2 border-emerald-600 flex items-center justify-center z-10 relative shadow-md">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @elseif($timelineItem['isCurrent'])
                                    <!-- Current (yellow circle) -->
                                    <div class="w-5 h-5 rounded-full bg-yellow-500 border-2 border-yellow-600 z-10 relative shadow-md ring-2 ring-yellow-200"></div>
                                @else
                                    <!-- Upcoming (grey circle) -->
                                    <div class="w-5 h-5 rounded-full bg-gray-300 border-2 border-gray-400 z-10 relative"></div>
                                @endif
                                
                                @if(!$loop->last)
                                    @php
                                        $nextItem = $timelineStatuses[$index + 1] ?? null;
                                        $lineColor = ($timelineItem['completed'] && ($nextItem && ($nextItem['completed'] || $nextItem['isCurrent']))) ? 'bg-emerald-600' : 'bg-gray-300';
                                    @endphp
                                    <div class="w-0.5 h-full {{ $lineColor }} mt-2 absolute top-5"></div>
                                @endif
                            </div>
                            
                            <!-- Timeline Content -->
                            <div class="flex-1 pb-4 min-w-0">
                                <div class="mb-2">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="font-semibold text-gray-900 text-base">
                                            {{ $allStatuses[$timelineItem['status']]['label'] ?? $statusLabels[$timelineItem['status']] ?? $timelineItem['status'] }}
                                    </span>
                                        @if($timelineItem['isCurrent'])
                                            <span class="px-2.5 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full border border-yellow-200">
                                                Sedang Berlangsung
                                    </span>
                                        @endif
                                    </div>
                                    @if($timelineItem['date'])
                                        <p class="text-xs text-gray-500 font-medium mb-1">
                                            {{ $timelineItem['date']->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                    @if($timelineItem['updatedBy'])
                                        <p class="text-xs text-gray-500">Oleh: {{ $timelineItem['updatedBy'] }}</p>
                                    @endif
                                </div>
                                
                                @if($timelineItem['note'])
                                    <p class="text-sm text-gray-700 mt-2 bg-gray-50 rounded-lg p-2 border border-gray-200">{{ $timelineItem['note'] }}</p>
                                @endif
                                
                                @if($timelineItem['image'])
                                    <div class="mt-3">
                                        <a href="{{ Storage::url($timelineItem['image']) }}" target="_blank" class="inline-block">
                                            <img src="{{ Storage::url($timelineItem['image']) }}" alt="Foto progress" 
                                                 class="h-24 w-auto rounded-lg hover:opacity-80 transition-opacity border border-gray-200 shadow-sm">
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Divider -->
            <hr class="border-gray-200">

            <!-- Comments (Admin only) -->
            @if($comments->isNotEmpty())
                <div class="bg-white rounded-xl border-2 border-gray-200 p-6 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b-2 border-gray-200">Komentar Admin</h3>
                    
                    <div class="space-y-4">
                        @foreach($comments as $comment)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-semibold text-gray-900">
                                        {{ $comment->sender_name ?? 'Admin' }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $comment->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $comment->message }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Divider -->
                <hr class="border-gray-200">
            @endif

            <!-- 6️⃣ Bigger CTA Button -->
            <div class="pt-2">
                <a href="{{ route('complaints.index') }}" 
                   class="block w-full px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg rounded-lg shadow-md hover:shadow-lg transition-all duration-200 text-center">
                    Buat Pengaduan Baru
                </a>
            </div>
        </div>
    </div>
</x-sections.section>

<!-- 7️⃣ Footer Spacing -->
<div class="mt-16"></div>

@push('scripts')
<script>
    // Copy tracking code functionality
    document.getElementById('copyTrackingCode')?.addEventListener('click', function() {
        const code = this.getAttribute('data-code');
        const copyText = document.getElementById('copyText');
        const button = this;
        
        navigator.clipboard.writeText(code).then(function() {
            const originalText = copyText.textContent;
            copyText.textContent = 'Tersalin!';
            button.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
            button.classList.add('bg-green-500', 'hover:bg-green-600');
            
            setTimeout(function() {
                copyText.textContent = originalText;
                button.classList.remove('bg-green-500', 'hover:bg-green-600');
                button.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
            }, 2000);
        }).catch(function(err) {
            console.error('Failed to copy:', err);
            alert('Gagal menyalin kode. Silakan salin manual: ' + code);
        });
    });
</script>
@endpush
@endsection

