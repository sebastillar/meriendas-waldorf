<?php

namespace App\Filament\Resources\DiaSinClaseResource\Pages;

use App\Filament\Resources\DiaSinClaseResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListDiaSinClase extends ListRecords
{
    protected static string $resource = DiaSinClaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

