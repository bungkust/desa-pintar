<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

class BulkDeletePosts extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-trash';

    protected static string $view = 'filament.resources.post-resource.pages.bulk-delete-posts';

    protected static ?string $title = 'Konfirmasi Hapus Posts';

    protected static string $resource = PostResource::class;

    public Collection $posts;

    public function mount(): void
    {
        $postIds = session('bulk_delete_post_ids', []);

        if (empty($postIds)) {
            Notification::make()
                ->title('Tidak ada post yang dipilih')
                ->warning()
                ->send();

            $this->redirect(PostResource::getUrl('index'));
            return;
        }

        $this->posts = Post::whereIn('id', $postIds)->get();

        if ($this->posts->isEmpty()) {
            Notification::make()
                ->title('Posts tidak ditemukan')
                ->warning()
                ->send();

            $this->redirect(PostResource::getUrl('index'));
            return;
        }
    }

    protected function getActions(): array
    {
        return [
            Action::make('cancel')
                ->label('Batal')
                ->color('gray')
                ->url(PostResource::getUrl('index')),

            Action::make('confirmDelete')
                ->label('Ya, Hapus Semua')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Hapus Posts')
                ->modalDescription('Apakah Anda yakin ingin menghapus semua posts yang dipilih? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->action(function () {
                    $count = $this->posts->count();

                    foreach ($this->posts as $post) {
                        $post->delete();
                    }

                    // Clear session
                    session()->forget('bulk_delete_post_ids');

                    Notification::make()
                        ->title("{$count} posts berhasil dihapus")
                        ->success()
                        ->send();

                    $this->redirect(PostResource::getUrl('index'));
                }),
        ];
    }
}
