<?php

namespace App\Filament\Resources\ComplaintCommentResource\Pages;

use App\Filament\Resources\ComplaintCommentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewComplaintComment extends ViewRecord
{
    protected static string $resource = ComplaintCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
