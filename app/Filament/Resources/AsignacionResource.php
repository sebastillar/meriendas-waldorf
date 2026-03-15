<?php

namespace App\Filament\Resources;

use App\Domain\Models\Asignacion;
use App\Domain\Services\AlumnoService;
use App\Domain\Services\GeneradorAgendaService;
use App\Domain\Services\IntercambioService;
use App\Filament\Resources\AsignacionResource\Pages;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AsignacionResource extends Resource
{
    protected static ?string $model = Asignacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Asignaciones';

    protected static ?string $modelLabel = 'Asignación';

    protected static ?string $pluralModelLabel = 'Asignaciones';

    protected static ?string $navigationGroup = 'Agenda';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        $alumnoService = app(AlumnoService::class);
        $intercambioService = app(IntercambioService::class);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('alumnoFruta.nombre')
                    ->label('Fruta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alumnoElaboracion.nombre')
                    ->label('Elaboración')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cereal')
                    ->label('Cereal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge(),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                Tables\Filters\Filter::make('mes_anio')
                    ->form([
                        Forms\Components\Select::make('anio')
                            ->label('Año')
                            ->options(function () {
                                $anio = (int) date('Y');
                                return array_combine(
                                    range($anio - 2, $anio + 1),
                                    range($anio - 2, $anio + 1)
                                );
                            })
                            ->default((int) date('Y')),
                        Forms\Components\Select::make('mes')
                            ->label('Mes')
                            ->options([
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                            ])
                            ->default((int) date('n')),
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['anio']) && !empty($data['mes'])) {
                            $desde = Carbon::createFromDate((int) $data['anio'], (int) $data['mes'], 1);
                            $hasta = $desde->copy()->endOfMonth();
                            $query->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()]);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('intercambiar')
                    ->label('Intercambiar')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Hidden::make('asignacion_id'),
                        Forms\Components\Select::make('rol')
                            ->label('Rol')
                            ->options([
                                'fruta' => 'Fruta',
                                'elaboracion' => 'Elaboración',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),
                        Forms\Components\Select::make('alumno_nuevo_id')
                            ->label('Alumno con quien intercambiar')
                            ->options(function (Forms\Get $get) use ($alumnoService) {
                                $asignacionId = $get('asignacion_id');
                                $rol = $get('rol');
                                if (!$asignacionId || !$rol) {
                                    return [];
                                }
                                $asignacion = Asignacion::with('alumnoFruta', 'alumnoElaboracion')->find($asignacionId);
                                if (!$asignacion) {
                                    return [];
                                }
                                $excluirIds = [$asignacion->alumno_fruta_id, $asignacion->alumno_elaboracion_id];
                                return $alumnoService->activos()
                                    ->unique('id')
                                    ->reject(fn ($a) => in_array($a->id, $excluirIds, true))
                                    ->mapWithKeys(fn ($a) => [$a->id => $a->nombre])
                                    ->all();
                            })
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->live(),
                        Forms\Components\Textarea::make('motivo')
                            ->label('Motivo')
                            ->rows(3),
                    ])
                    ->fillForm(fn (Asignacion $record): array => ['asignacion_id' => $record->id])
                    ->action(function (Asignacion $record, array $data) use ($intercambioService) {
                        try {
                            $intercambioService->intercambiar(
                                (int) $record->id,
                                $data['rol'],
                                (int) $data['alumno_nuevo_id'],
                                $data['motivo'] ?? null
                            );
                            Notification::make()
                                ->title('Intercambio realizado')
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
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsignaciones::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
