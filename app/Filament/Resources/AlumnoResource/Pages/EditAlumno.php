<?php

namespace App\Filament\Resources\AlumnoResource\Pages;

use App\Filament\Resources\AlumnoResource;
use App\Domain\Services\AlumnoService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlumno extends EditRecord
{
    protected static string $resource = AlumnoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function () {
                    app(AlumnoService::class)->eliminar((int) $this->record->id);
                    redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return app(AlumnoService::class)->actualizar((int) $record->id, $data);
    }
}
