<?php

namespace App\Filament\Resources\CerealPorDiaResource\Pages;

use App\Filament\Resources\CerealPorDiaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCerealPorDias extends ListRecords
{
    protected static string $resource = CerealPorDiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
