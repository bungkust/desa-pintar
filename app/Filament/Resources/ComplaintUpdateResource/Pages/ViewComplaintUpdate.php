<?php

namespace App\Filament\Resources\ComplaintUpdateResource\Pages;

use App\Filament\Resources\ComplaintUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewComplaintUpdate extends ViewRecord
{
    protected static string $resource = ComplaintUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
