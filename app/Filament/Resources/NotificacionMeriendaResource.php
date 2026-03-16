<?php

namespace App\Filament\Resources;

use App\Domain\Models\NotificacionMerienda;
use App\Domain\Services\NotificacionMeriendaManager;
use App\Filament\Resources\NotificacionMeriendaResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificacionMeriendaResource extends Resource
{
    protected static ?string $model = NotificacionMerienda::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Notificaciones de merienda';

    protected static ?string $modelLabel = 'Notificación de merienda';

    protected static ?string $pluralModelLabel = 'Notificaciones de merienda';

    protected static ?string $navigationGroup = 'Gestión';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha_envio_programada')
                    ->label('Fecha programada')
                    ->disabled(),
                Forms\Components\TextInput::make('tipo')
                    ->label('Tipo')
                    ->disabled(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->disabled(),
                Forms\Components\TextInput::make('rol')
                    ->label('Rol')
                    ->disabled(),
                Forms\Components\TextInput::make('nombre_alumno')
                    ->label('Alumno')
                    ->disabled(),
                Forms\Components\TextInput::make('estado')
                    ->label('Estado')
                    ->disabled(),
                Forms\Components\TextInput::make('intentos')
                    ->label('Intentos')
                    ->numeric()
                    ->disabled(),
                Forms\Components\Textarea::make('error_ultimo_intento')
                    ->label('Error último intento')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_envio_programada')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre_alumno')
                    ->label('Alumno')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 'enviado',
                        'warning' => 'pendiente',
                        'danger' => 'fallido',
                    ]),
                Tables\Columns\TextColumn::make('intentos')
                    ->label('Intentos')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ultimo_intento_at')
                    ->label('Último intento')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'enviado' => 'Enviado',
                        'fallido' => 'Fallido',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('reenviar')
                    ->label('Reenviar notificación')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->action(function (NotificacionMerienda $record) {
                        app(NotificacionMeriendaManager::class)->reenviar($record);
                    }),
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
            'index' => Pages\ListNotificaciones::route('/'),
            'view' => Pages\ViewNotificacion::route('/{record}'),
        ];
    }
}

