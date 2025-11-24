<?php

namespace App\Filament\Resources\ComplaintCommentResource\Pages;

use App\Filament\Resources\ComplaintCommentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComplaintComment extends EditRecord
{
    protected static string $resource = ComplaintCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
