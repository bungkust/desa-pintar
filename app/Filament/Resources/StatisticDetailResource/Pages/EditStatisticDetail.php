<?php

namespace App\Filament\Resources\StatisticDetailResource\Pages;

use App\Filament\Resources\StatisticDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatisticDetail extends EditRecord
{
    protected static string $resource = StatisticDetailResource::class;

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
}
