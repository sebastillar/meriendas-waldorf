<?php

namespace App\Filament\Resources\FamiliaResource\Pages;

use App\Filament\Resources\FamiliaResource;
use App\Domain\Services\FamiliaService;
use App\Domain\Services\RecolectaAportesService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFamilia extends EditRecord
{
    protected static string $resource = FamiliaResource::class;

    public function getTitle(): string
    {
        $nombre = $this->record->nombre_para_listado ?? ('Familia #' . $this->record->id);
        return 'Editar Familia de ' . $nombre;
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function () {
                    app(FamiliaService::class)->eliminar((int) $this->record->id);
                    redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $familiaBeneficiariaId = (int) ($data['familia_regalo_id'] ?? 0);
        $data['aportes_alumno_ids'] = $familiaBeneficiariaId > 0
            ? app(RecolectaAportesService::class)->getAlumnoIdsQueAportaron($familiaBeneficiariaId)
            : [];
        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $aportes = $data['aportes_alumno_ids'] ?? [];
        unset($data['aportes_alumno_ids']);

        $service = app(FamiliaService::class);
        $updated = $service->actualizar((int) $record->id, $data);

        $familiaBeneficiariaId = (int) $record->familia_regalo_id;
        if ($familiaBeneficiariaId > 0) {
            app(RecolectaAportesService::class)->syncAportes($familiaBeneficiariaId, array_map('intval', $aportes));
        }

        return $updated;
    }
}
