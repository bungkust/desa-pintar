<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use App\Models\Complaint;
use App\Models\ComplaintComment;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Log;

class ComplaintComments extends Page
{
    protected static string $resource = ComplaintResource::class;

    protected static string $view = 'filament.resources.complaint-resource.pages.complaint-comments';

    public Complaint $record;
    public $comments = [];
    public $message = '';

    public function mount(Complaint $record): void
    {
        $this->record = $record;
        $this->loadComments();
    }

    public function loadComments(): void
    {
        $this->comments = $this->record->comments()
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function addComment(): void
    {
        $this->validate([
            'message' => 'required|string|min:5',
        ]);

        ComplaintComment::create([
            'complaint_id' => $this->record->id,
            'sender_type' => 'admin',
            'sender_name' => auth()->user()->name,
            'message' => $this->message,
        ]);

        Log::info('Complaint comment added via page', [
            'complaint_id' => $this->record->id,
            'user_id' => auth()->id(),
        ]);

        Notification::make()
            ->title('Komentar berhasil ditambahkan')
            ->success()
            ->send();

        $this->message = ''; // Reset form
        $this->loadComments();
    }

    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali ke Daftar')
                ->url(ComplaintResource::getUrl('index'))
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    public function getTitle(): string
    {
        return 'Komentar - ' . $this->record->title;
    }
}
