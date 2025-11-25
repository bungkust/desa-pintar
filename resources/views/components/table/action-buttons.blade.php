@props([
    'record',
    'viewUrl' => null,
    'editUrl' => null,
])

@php
    $recordId = $record->id ?? $record->getKey();
    $viewUrl = $viewUrl ?? route('filament.admin.resources.complaints.view', $record);
    $editUrl = $editUrl ?? route('filament.admin.resources.complaints.edit', $record);
@endphp

<div class="flex items-center justify-end gap-1" x-data="{ open: false }">
    <!-- View Icon -->
    <a href="{{ $viewUrl }}" 
       class="p-2 rounded hover:bg-gray-100 transition-colors"
       title="View">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
    </a>

    <!-- Edit Icon -->
    <a href="{{ $editUrl }}" 
       class="p-2 rounded hover:bg-gray-100 transition-colors"
       title="Edit">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5h2m2 0h2m-6 4h6m-6 4h6m-6 4h3m3-12V3m0 0h-2m2 0h2m-2 0v2m0-2V3m-2 0h2m-2 0h-2m2 0V1m0 2v2m0-2h-2m2 0h2" />
        </svg>
    </a>

    <!-- Kebab Menu Button -->
    <div class="relative" x-data="{ open: false }">
        <button @click.stop="open = !open"
                @click.away="open = false"
                class="p-2 rounded hover:bg-gray-100 transition-colors"
                title="More actions">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open"
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             x-cloak
             class="absolute right-0 mt-2 w-44 bg-white shadow-lg rounded-md py-2 z-50 border border-gray-200 whitespace-nowrap text-sm">
            
            <!-- Update Status -->
            <button wire:click="mountTableAction('updateStatus', '{{ $recordId }}')"
                    class="w-full text-left block px-4 py-2 text-gray-700 hover:bg-gray-100 cursor-pointer transition-colors">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Update Status</span>
                </div>
            </button>

            <!-- Assign Petugas -->
            <button wire:click="mountTableAction('assignPetugas', '{{ $recordId }}')"
                    class="w-full text-left block px-4 py-2 text-gray-700 hover:bg-gray-100 cursor-pointer transition-colors">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                    </svg>
                    <span>Assign Petugas</span>
                </div>
            </button>

            <!-- Komentar -->
            <button wire:click="mountTableAction('komentar', '{{ $recordId }}')"
                    class="w-full text-left block px-4 py-2 text-gray-700 hover:bg-gray-100 cursor-pointer transition-colors">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                    </svg>
                    <span>Komentar</span>
                </div>
            </button>

            <!-- Riwayat Update -->
            <button wire:click="mountTableAction('riwayatUpdate', '{{ $recordId }}')"
                    class="w-full text-left block px-4 py-2 text-gray-700 hover:bg-gray-100 cursor-pointer transition-colors">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Riwayat Update</span>
                </div>
            </button>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-1"></div>

            <!-- Delete (Hapus) -->
            <button wire:click="mountTableAction('delete', '{{ $recordId }}')"
                    wire:confirm="Apakah Anda yakin ingin menghapus pengaduan ini?"
                    class="w-full text-left block px-4 py-2 text-red-600 hover:bg-red-50 cursor-pointer transition-colors">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                    <span>Hapus</span>
                </div>
            </button>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>



