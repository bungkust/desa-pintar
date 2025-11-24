<?php

namespace App\Filament\Resources\StatisticDetailResource\Pages;

use App\Filament\Resources\StatisticDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStatisticDetail extends CreateRecord
{
    protected static string $resource = StatisticDetailResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
