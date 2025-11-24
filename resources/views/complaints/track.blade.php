@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<x-sections.page-header 
    title="Lacak Pengaduan"
    description="Cek status pengaduan Anda menggunakan kode tracking"
    gradient="from-emerald-50 via-teal-50 to-cyan-50"
/>

<x-sections.section spacing="py-12 md:py-16 lg:py-20">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <!-- Tracking Code Header -->
            <div class="mb-6 pb-6 border-b">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Kode Tracking: {{ $complaint->tracking_code }}</h2>
                        <p class="text-sm text-gray-600 mt-1">Dibuat: {{ $complaint->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @php
                            $statusColors = [
                                'backlog' => 'bg-gray-100 text-gray-800',
                                'verification' => 'bg-yellow-100 text-yellow-800',
                                'todo' => 'bg-blue-100 text-blue-800',
                                'in_progress' => 'bg-indigo-100 text-indigo-800',
                                'done' => 'bg-emerald-100 text-emerald-800',
                                'rejected' => 'bg-red-100 text-red-800',
                            ];
                            $statusLabels = [
                                'backlog' => 'Backlog',
                                'verification' => 'Verifikasi',
                                'todo' => 'To Do',
                                'in_progress' => 'Sedang Dikerjakan',
                                'done' => 'Selesai',
                                'rejected' => 'Ditolak',
                            ];
                            $slaColor = $complaint->isOverdue() ? 'text-red-600' : ($complaint->isNearingDeadline() ? 'text-yellow-600' : 'text-emerald-600');
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$complaint->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$complaint->status] ?? $complaint->status }}
                        </span>
                        @if($complaint->sla_deadline)
                            <span class="text-sm {{ $slaColor }} font-medium">
                                SLA: {{ $complaint->sla_deadline->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Complaint Details -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Pengaduan</h3>
                
                <div class="space-y-4">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Kategori:</span>
                        <span class="ml-2 text-gray-900">{{ ucfirst($complaint->category) }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-700">Judul:</span>
                        <p class="mt-1 text-gray-900">{{ $complaint->title }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-700">Deskripsi:</span>
                        <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $complaint->description }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-700">Lokasi:</span>
                        <p class="mt-1 text-gray-900">{{ $complaint->location_text }}</p>
                    </div>
                    
                    @if($complaint->rt || $complaint->rw)
                        <div>
                            <span class="text-sm font-medium text-gray-700">RT/RW:</span>
                            <span class="ml-2 text-gray-900">
                                @if($complaint->rt)RT {{ $complaint->rt }}@endif
                                @if($complaint->rt && $complaint->rw) / @endif
                                @if($complaint->rw)RW {{ $complaint->rw }}@endif
                            </span>
                        </div>
                    @endif

                    @if($complaint->images && count($complaint->images) > 0)
                        <div>
                            <span class="text-sm font-medium text-gray-700">Foto Pendukung:</span>
                            <div class="mt-2 grid grid-cols-3 gap-4">
                                @foreach($complaint->images as $image)
                                    <a href="{{ Storage::url($image) }}" target="_blank" class="block">
                                        <img src="{{ Storage::url($image) }}" alt="Foto pengaduan" 
                                             class="w-full h-32 object-cover rounded-lg hover:opacity-80 transition-opacity">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline Status</h3>
                
                <div class="space-y-4">
                    @foreach($updates as $update)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-emerald-600"></div>
                                @if(!$loop->last)
                                    <div class="w-0.5 h-full bg-gray-200 mt-2"></div>
                                @endif
                            </div>
                            <div class="flex-1 pb-4">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-medium text-gray-900">
                                        {{ $statusLabels[$update->status_to] ?? $update->status_to }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ $update->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                @if($update->note)
                                    <p class="text-sm text-gray-700 mt-1">{{ $update->note }}</p>
                                @endif
                                @if($update->updatedBy)
                                    <p class="text-xs text-gray-500 mt-1">Oleh: {{ $update->updatedBy->name }}</p>
                                @endif
                                @if($update->image)
                                    <div class="mt-2">
                                        <a href="{{ Storage::url($update->image) }}" target="_blank" class="inline-block">
                                            <img src="{{ Storage::url($update->image) }}" alt="Foto progress" 
                                                 class="h-24 w-auto rounded-lg hover:opacity-80 transition-opacity">
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Comments (Admin only) -->
            @if($comments->isNotEmpty())
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Komentar Admin</h3>
                    
                    <div class="space-y-4">
                        @foreach($comments as $comment)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-900">
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
            @endif

            <!-- Actions -->
            <div class="pt-6 border-t">
                <a href="{{ route('complaints.create') }}" 
                   class="inline-block px-6 py-3 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    Buat Pengaduan Baru
                </a>
            </div>
        </div>
    </div>
</x-sections.section>
@endsection

