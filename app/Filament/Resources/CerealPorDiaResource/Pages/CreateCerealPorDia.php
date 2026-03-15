<?php

namespace App\Filament\Resources\CerealPorDiaResource\Pages;

use App\Filament\Resources\CerealPorDiaResource;
use App\Domain\Services\CerealPorDiaService;
use Filament\Resources\Pages\CreateRecord;

class CreateCerealPorDia extends CreateRecord
{
    protected static string $resource = CerealPorDiaResource::class;

    public function __construct(
        private CerealPorDiaService $cerealPorDiaService
    ) {}

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return $this->cerealPorDiaService->crear($data);
    }
}
