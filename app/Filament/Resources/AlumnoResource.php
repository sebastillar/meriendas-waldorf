<?php

namespace App\Filament\Resources;

use App\Domain\Models\Alumno;
use App\Domain\Services\AlumnoService;
use App\Filament\Resources\AlumnoResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AlumnoResource extends Resource
{
    protected static ?string $model = Alumno::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Alumnos';

    protected static ?string $modelLabel = 'Alumno';

    protected static ?string $pluralModelLabel = 'Alumnos';

    protected static ?string $navigationGroup = 'Gestión';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('fecha_cumpleanos')
                    ->label('Fecha de cumpleaños'),
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_cumpleanos')
                    ->label('Cumpleaños')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('darDeBaja')
                    ->label('Dar de baja')
                    ->icon('heroicon-o-arrow-down-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Dar de baja alumno')
                    ->modalDescription('Se marcará el alumno como inactivo. No se tendrá en cuenta en la agenda pública ni en las asignaciones futuras; se recalcularán estas últimas.')
                    ->visible(fn (Alumno $record): bool => $record->activo)
                    ->action(function (Alumno $record) {
                        try {
                            app(AlumnoService::class)->darDeBaja((int) $record->id);
                            \Filament\Notifications\Notification::make()
                                ->title('Alumno dado de baja')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('darDeAlta')
                    ->label('Dar de alta')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Dar de alta alumno')
                    ->modalDescription('Se marcará el alumno como activo y se recalcularán las asignaciones futuras.')
                    ->visible(fn (Alumno $record): bool => !$record->activo)
                    ->action(function (Alumno $record) {
                        try {
                            app(AlumnoService::class)->darDeAlta((int) $record->id);
                            \Filament\Notifications\Notification::make()
                                ->title('Alumno dado de alta')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $service = app(AlumnoService::class);
                            foreach ($records as $record) {
                                $service->eliminar((int) $record->id);
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlumnos::route('/'),
            'edit' => Pages\EditAlumno::route('/{record}/edit'),
        ];
    }
}
