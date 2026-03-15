<?php

namespace App\Filament\Resources;

use App\Domain\Models\CerealPorDia;
use App\Domain\Services\CerealPorDiaService;
use App\Filament\Resources\CerealPorDiaResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CerealPorDiaResource extends Resource
{
    protected static ?string $model = CerealPorDia::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Cereales por día';

    protected static ?string $modelLabel = 'Cereal por día';

    protected static ?string $pluralModelLabel = 'Cereales por día';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $diasSemana = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        return $form
            ->schema([
                Forms\Components\Select::make('dia_semana')
                    ->label('Día de la semana')
                    ->options($diasSemana)
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('cereal')
                    ->label('Cereal')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        $diasSemana = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dia_semana')
                    ->label('Día')
                    ->formatStateUsing(fn (int $state): string => $diasSemana[$state] ?? (string) $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('cereal')
                    ->label('Cereal')
                    ->searchable(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->defaultSort('dia_semana')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $service = app(\App\Domain\Services\CerealPorDiaService::class);
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
            'index' => Pages\ListCerealPorDias::route('/'),
            'create' => Pages\CreateCerealPorDia::route('/create'),
            'edit' => Pages\EditCerealPorDia::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->orderBy('dia_semana');
    }
}
