<?php

namespace App\Filament\Resources;

use App\Domain\Models\ConfiguracionCalendario;
use App\Filament\Resources\CalendarioEscolarResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CalendarioEscolarResource extends Resource
{
    protected static ?string $model = ConfiguracionCalendario::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Calendario escolar';

    protected static ?string $modelLabel = 'Configuración de calendario';

    protected static ?string $pluralModelLabel = 'Calendario escolar';

    protected static ?string $navigationGroup = 'Gestión';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('anio')
                    ->label('Año')
                    ->numeric()
                    ->required()
                    ->minValue(2000)
                    ->maxValue(2100),
                Forms\Components\DatePicker::make('fecha_inicio_clases')
                    ->label('Fecha de inicio de clases')
                    ->required(),
                Forms\Components\DatePicker::make('fecha_fin_clases')
                    ->label('Fecha de fin de clases'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('anio')
                    ->label('Año')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_inicio_clases')
                    ->label('Inicio de clases')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin_clases')
                    ->label('Fin de clases')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // En el futuro se puede agregar un RelationManager para días sin clase filtrados por año.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalendarioEscolar::route('/'),
            'create' => Pages\CreateCalendarioEscolar::route('/create'),
            'edit' => Pages\EditCalendarioEscolar::route('/{record}/edit'),
        ];
    }
}

