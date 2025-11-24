<?php

namespace App\Filament\Resources\StatisticDetailResource\Pages;

use App\Filament\Resources\StatisticDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatisticDetails extends ListRecords
{
    protected static string $resource = StatisticDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
