<?php

namespace App\Filament\Resources\CerealPorDiaResource\Pages;

use App\Filament\Resources\CerealPorDiaResource;
use App\Domain\Services\CerealPorDiaService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCerealPorDia extends EditRecord
{
    protected static string $resource = CerealPorDiaResource::class;

    public function __construct(
        private CerealPorDiaService $cerealPorDiaService
    ) {}

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function () {
                    $this->cerealPorDiaService->eliminar((int) $this->record->id);
                    redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return $this->cerealPorDiaService->actualizar((int) $record->id, $data);
    }
}
