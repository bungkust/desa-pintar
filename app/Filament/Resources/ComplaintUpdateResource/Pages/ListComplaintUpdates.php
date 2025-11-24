<?php

namespace App\Filament\Resources\ComplaintUpdateResource\Pages;

use App\Filament\Resources\ComplaintUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComplaintUpdates extends ListRecords
{
    protected static string $resource = ComplaintUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // View-only resource
        ];
    }
}
