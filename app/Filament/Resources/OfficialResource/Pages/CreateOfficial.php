<?php

namespace App\Filament\Resources\OfficialResource\Pages;

use App\Filament\Resources\OfficialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOfficial extends CreateRecord
{
    protected static string $resource = OfficialResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

