<?php

namespace App\Filament\Resources\ComplaintUpdateResource\Pages;

use App\Filament\Resources\ComplaintUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComplaintUpdate extends EditRecord
{
    protected static string $resource = ComplaintUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
