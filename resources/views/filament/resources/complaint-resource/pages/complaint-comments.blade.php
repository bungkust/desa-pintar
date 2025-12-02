<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Complaint Info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-950 dark:text-white mb-6">Informasi Pengaduan</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-950 dark:text-white">
                        Kode Pengaduan
                    </label>
                    <div class="text-sm font-mono bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600">
                        {{ $record->tracking_code }}
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-950 dark:text-white">
                        Status
                    </label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($record->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200
                        @elseif($record->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200
                        @elseif($record->status === 'resolved') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200
                        @elseif($record->status === 'backlog') bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                        {{ ucfirst($record->status) }}
                    </span>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-950 dark:text-white">
                    Judul Pengaduan
                </label>
                <div class="text-lg font-semibold text-gray-950 dark:text-white mt-1">
                    {{ $record->title }}
                </div>
            </div>
        </div>

        <!-- Add Comment Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-950 dark:text-white mb-6">Tambah Komentar Baru</h3>

            <form wire:submit.prevent="addComment" class="space-y-6">
                <div class="space-y-2">
                    <label for="message" class="block text-sm font-medium text-gray-950 dark:text-white">
                        Komentar <span class="text-danger-600">*</span>
                    </label>

                    <textarea
                        wire:model="message"
                        id="message"
                        rows="4"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm placeholder-gray-400 dark:placeholder-gray-500 resize-vertical"
                        placeholder="Tulis komentar Anda di sini..."
                        required
                    ></textarea>

                    @error('message')
                        <p class="text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-70 disabled:cursor-wait text-white text-sm font-medium rounded-lg focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors duration-200"
                    >
                        <svg wire:loading.remove class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <svg wire:loading class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span wire:loading.remove>Kirim Komentar</span>
                        <span wire:loading>Mengirim...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Comments List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-950 dark:text-white mb-6">Riwayat Komentar</h3>

            @if(empty($comments))
                <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                    <div class="flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Belum ada komentar
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">
                        Belum ada komentar untuk pengaduan ini. Tambahkan komentar pertama untuk memulai diskusi.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($comments as $comment)
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-2">
                            <div class="flex items-center justify-center w-6 h-6 bg-primary-100 dark:bg-primary-900/50 rounded-full">
                                <svg class="w-3 h-3 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $comment['sender_name'] }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($comment['sender_type'] === 'admin')
                                            bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-200
                                        @else
                                            bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                        @endif">
                                        {{ $comment['sender_type'] === 'admin' ? 'Admin' : 'User' }}
                                    </span>
                                </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                                    {{ \Carbon\Carbon::parse($comment['created_at'])->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">
                                {{ $comment['message'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>