<?php

namespace App\Filament\Resources;

use App\Domain\Models\DiaSinClase;
use App\Filament\Resources\DiaSinClaseResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DiaSinClaseResource extends Resource
{
    protected static ?string $model = DiaSinClase::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Días sin clase';

    protected static ?string $modelLabel = 'Día sin clase';

    protected static ?string $pluralModelLabel = 'Días sin clase';

    protected static ?string $navigationGroup = 'Gestión';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->required(),
                Forms\Components\TextInput::make('motivo')
                    ->label('Motivo')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('motivo')
                    ->label('Motivo')
                    ->wrap()
                    ->limit(80),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiaSinClase::route('/'),
            'create' => Pages\CreateDiaSinClase::route('/create'),
            'edit' => Pages\EditDiaSinClase::route('/{record}/edit'),
        ];
    }
}

