<?php

namespace App\Filament\Resources\QuickLinkResource\Pages;

use App\Filament\Resources\QuickLinkResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuickLink extends CreateRecord
{
    protected static string $resource = QuickLinkResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

