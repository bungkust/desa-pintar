<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    public $selectedRecords = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function performBulkDelete()
    {
        if (empty($this->selectedRecords)) {
            Notification::make()
                ->title('Tidak ada post yang dipilih')
                ->warning()
                ->send();
            return;
        }

        $count = count($this->selectedRecords);
        Post::whereIn('id', $this->selectedRecords)->delete();

        $this->selectedRecords = []; // Clear selection

        Notification::make()
            ->title("{$count} posts berhasil dihapus")
            ->success()
            ->send();

        // Refresh the table
        $this->resetTable();
    }
}

