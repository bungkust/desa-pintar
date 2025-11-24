<?php

namespace App\Filament\Resources\OfficialResource\Pages;

use App\Filament\Resources\OfficialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditOfficial extends EditRecord
{
    protected static string $resource = OfficialResource::class;

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

    /**
     * Clear cache after saving Official
     * This ensures cache is cleared even if observer doesn't fire
     */
    protected function afterSave(): void
    {
        $record = $this->getRecord();
        
        // Clear lurah cache if this is or was the Lurah
        if ($record->position === 'Lurah') {
            Cache::forget('lurah_official');
        }
        
        // Also clear in case position was changed
        if ($record->wasChanged('position')) {
            $originalPosition = $record->getOriginal('position');
            if ($originalPosition === 'Lurah' || $record->position === 'Lurah') {
                Cache::forget('lurah_official');
            }
        }
    }
}

