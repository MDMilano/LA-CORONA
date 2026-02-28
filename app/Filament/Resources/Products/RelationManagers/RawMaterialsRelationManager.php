<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RawMaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'rawMaterials';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('raw_material_id')
                    ->relationship('rawMaterial', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a raw material to attach')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('current_stock')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('m³'),
                    ]),
                TextInput::make('volume_required')
                    ->label('Volume Required (m³)')
                    ->numeric()
                    ->required()
                    ->minValue(0.001)
                    ->step(0.001)
                    ->suffix('m³')
                    ->placeholder('e.g. 1.5'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Raw Material Name')
                    ->searchable(),
                TextColumn::make('pivot.volume_required')
                    ->label('Volume Required (m³)')
                    ->numeric(3)
                    ->suffix('m³')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AttachAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
