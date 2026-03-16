<?php

namespace App\Filament\Resources\CalendarioEscolarResource\Pages;

use App\Filament\Resources\CalendarioEscolarResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListCalendarioEscolar extends ListRecords
{
    protected static string $resource = CalendarioEscolarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

