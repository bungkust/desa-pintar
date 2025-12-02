<x-filament-panels::page>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-8 h-8 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Konfirmasi Hapus Posts
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Anda akan menghapus {{ $posts->count() }} posts. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div class="text-sm text-red-800 dark:text-red-200">
                            <strong>Perhatian:</strong> Posts yang dihapus akan hilang permanen beserta semua data terkait seperti thumbnail dan konten.
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Posts yang akan dihapus ({{ $posts->count() }}):
                    </h3>

                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($posts as $post)
                            <div class="flex items-center justify-between bg-white dark:bg-gray-800 p-3 rounded border">
                                <div class="flex items-center space-x-3">
                                    @if($post->thumbnail)
                                        <img src="{{ asset('storage/' . $post->thumbnail) }}"
                                             alt="{{ $post->title }}"
                                             class="w-12 h-12 object-cover rounded">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">
                                            {{ $post->title }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            ID: {{ $post->id }} •
                                            @if($post->published_at)
                                                Dipublikasikan: {{ $post->published_at->format('d/m/Y H:i') }}
                                            @else
                                                Draft
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($post->published_at && $post->published_at->isPast())
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else
                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @endif">
                                        @if($post->published_at && $post->published_at->isPast())
                                            Published
                                        @else
                                            Draft
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <x-filament::button
                        wire:click="confirmDelete"
                        color="danger"
                        icon="heroicon-o-trash">
                        Hapus Semua Posts
                    </x-filament::button>

                    <x-filament::button
                        tag="a"
                        href="{{ \App\Filament\Resources\PostResource::getUrl('index') }}"
                        color="gray">
                        Batal
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
