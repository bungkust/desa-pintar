<x-filament-panels::page>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/50 mb-4">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        Hapus Posts
                    </h3>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Anda akan menghapus <strong>{{ $posts->count() }}</strong> posts. Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                            Posts yang akan dihapus:
                        </h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                            @foreach($posts as $post)
                                <li class="flex items-center">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2 flex-shrink-0"></span>
                                    {{ $post->title }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Klik "Ya, Hapus Semua" untuk melanjutkan atau "Batal" untuk kembali.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
