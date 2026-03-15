<?php

namespace App\Filament\Resources\FamiliaResource\Pages;

use App\Filament\Resources\FamiliaResource;
use App\Domain\Services\FamiliaService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateFamilia extends CreateRecord
{
    protected static string $resource = FamiliaResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $service = app(FamiliaService::class);
        return $service->crear($data);
    }

    protected function onValidationError(\Illuminate\Validation\ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }
}
