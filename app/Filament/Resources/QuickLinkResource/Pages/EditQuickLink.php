<?php

namespace App\Filament\Resources\QuickLinkResource\Pages;

use App\Filament\Resources\QuickLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditQuickLink extends EditRecord
{
    protected static string $resource = QuickLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        // Clear cache after save to ensure homepage shows updated data
        Cache::forget('quick_links');
    }
}

