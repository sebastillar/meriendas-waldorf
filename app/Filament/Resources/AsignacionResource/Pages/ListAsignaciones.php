<?php

namespace App\Filament\Resources\AsignacionResource\Pages;

use App\Domain\Services\GeneradorAgendaService;
use App\Filament\Resources\AsignacionResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAsignaciones extends ListRecords
{
    protected static string $resource = AsignacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generarMes')
                ->label('Generar asignaciones para mes')
                ->icon('heroicon-o-plus-circle')
                ->form([
                    Forms\Components\Select::make('anio')
                        ->label('Año')
                        ->options(function () {
                            $anio = (int) date('Y');
                            return array_combine(
                                range($anio, $anio + 2),
                                range($anio, $anio + 2)
                            );
                        })
                        ->required()
                        ->default((int) date('Y')),
                    Forms\Components\Select::make('mes')
                        ->label('Mes')
                        ->options([
                            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                        ])
                        ->required()
                        ->default((int) date('n')),
                ])
                ->action(function (array $data) {
                    try {
                        $generador = app(GeneradorAgendaService::class);
                        $generadas = $generador->generarParaMes((int) $data['anio'], (int) $data['mes']);
                        Notification::make()
                            ->title("Asignaciones generadas: {$generadas}")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
