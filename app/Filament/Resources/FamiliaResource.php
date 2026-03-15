<?php

namespace App\Filament\Resources;

use App\Domain\Models\Familia;
use App\Domain\Services\FamiliaService;
use App\Domain\Services\RecolectaAportesService;
use App\Filament\Resources\FamiliaResource\Pages;
use App\Filament\Resources\FamiliaResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FamiliaResource extends Resource
{
    protected static ?string $model = Familia::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Familias';

    protected static ?string $modelLabel = 'Familia';

    protected static ?string $pluralModelLabel = 'Familias';

    protected static ?string $navigationGroup = 'Gestión';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $familiaService = app(FamiliaService::class);
        $recolectaAportesService = app(RecolectaAportesService::class);
        $familiasActivas = $familiaService->activas()->keyBy('id')->map(fn (Familia $f) => $f->nombre_para_listado);

        return $form
            ->schema([
                Forms\Components\Section::make('Datos de contacto')
                    ->schema([
                        Forms\Components\TextInput::make('nombre_madre')
                            ->label('Nombre madre')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email_madre')
                            ->label('Correo madre')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nombre_padre')
                            ->label('Nombre padre')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email_padre')
                            ->label('Correo padre')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Datos para regalo')
                    ->schema([
                        Forms\Components\TextInput::make('banco')
                            ->label('Banco')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('numero_cuenta')
                            ->label('Número de cuenta')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tipo_cuenta')
                            ->label('Tipo de cuenta')
                            ->maxLength(100)
                            ->placeholder('Ej: Cuenta corriente'),
                        Forms\Components\TextInput::make('nombre_cuenta')
                            ->label('Nombre de la cuenta')
                            ->maxLength(255)
                            ->placeholder('Titular(es) de la cuenta'),
                        Forms\Components\TextInput::make('moneda')
                            ->label('Moneda')
                            ->maxLength(50)
                            ->placeholder('Ej: Pesos, USD'),
                        Forms\Components\Select::make('familia_regalo_id')
                            ->label('Regala a:')
                            ->options($familiasActivas->all())
                            ->native(false)
                            ->searchable(),
                        Forms\Components\CheckboxList::make('aportes_alumno_ids')
                            ->label('Alumnos que ya aportaron a esta colecta')
                            ->options(function (Forms\Get $get) use ($recolectaAportesService) {
                                $familiaRegaloId = (int) ($get('familia_regalo_id') ?? 0);
                                if ($familiaRegaloId === 0) {
                                    return [];
                                }
                                return $recolectaAportesService->alumnosParaAportes($familiaRegaloId);
                            })
                            ->columns(2)
                            ->visible(fn (Forms\Get $get) => (int) ($get('id') ?? 0) > 0 && (int) ($get('familia_regalo_id') ?? 0) > 0)
                            ->dehydrated(true)
                            ->helperText('Visible cuando esta familia tiene «Regala a» asignado (esta familia coordina la colecta). Marca los alumnos que ya aportaron.'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_para_listado')
                    ->label('Alumno')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_madre')
                    ->label('Email madre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_padre')
                    ->label('Email padre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('familiaParaRegalo.nombre_para_listado')
                    ->label('Regala a')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            RelationManagers\AlumnosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFamilias::route('/'),
            'create' => Pages\CreateFamilia::route('/create'),
            'edit' => Pages\EditFamilia::route('/{record}/edit'),
        ];
    }
}
