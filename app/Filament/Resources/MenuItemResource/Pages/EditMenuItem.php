<?php

namespace App\Filament\Resources\MenuItemResource\Pages;

use App\Filament\Resources\MenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuItem extends EditRecord
{
    protected static string $resource = MenuItemResource::class;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure URL is "#" if type is dropdown when loading form
        if (isset($data['type']) && $data['type'] === 'dropdown' && $data['url'] !== '#') {
            $data['url'] = '#';
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure URL is "#" for dropdown type
        if (isset($data['type']) && $data['type'] === 'dropdown') {
            $data['url'] = '#';
        }
        
        return $data;
    }
}
