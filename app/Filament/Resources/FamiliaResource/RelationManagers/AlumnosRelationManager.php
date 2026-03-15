<?php

namespace App\Filament\Resources\FamiliaResource\RelationManagers;

use App\Domain\Services\AlumnoService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AlumnosRelationManager extends RelationManager
{
    protected static string $relationship = 'alumnos';

    protected static ?string $title = 'Alumnos';

    protected static ?string $recordTitleAttribute = 'nombre';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['familia_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    })
                    ->using(function (array $data): \Illuminate\Database\Eloquent\Model {
                        return app(AlumnoService::class)->crear($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => $data)
                    ->using(function (\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model {
                        return app(AlumnoService::class)->actualizar((int) $record->id, $data);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->action(function (\Illuminate\Database\Eloquent\Model $record) {
                        app(AlumnoService::class)->eliminar((int) $record->id);
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
}
