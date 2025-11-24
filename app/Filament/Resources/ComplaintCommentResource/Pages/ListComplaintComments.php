<?php

namespace App\Filament\Resources\ComplaintCommentResource\Pages;

use App\Filament\Resources\ComplaintCommentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComplaintComments extends ListRecords
{
    protected static string $resource = ComplaintCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // View-only resource
        ];
    }
}
