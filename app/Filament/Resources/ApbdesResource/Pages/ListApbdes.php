<?php

namespace App\Filament\Resources\ApbdesResource\Pages;

use App\Filament\Resources\ApbdesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApbdes extends ListRecords
{
    protected static string $resource = ApbdesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

