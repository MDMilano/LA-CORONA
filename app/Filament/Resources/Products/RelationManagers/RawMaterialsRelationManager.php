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
use Filament\Schemas\Components\Group;
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
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Group::make([
                    TextInput::make('current_stock')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix('m³'),
                    TextInput::make('volume_required')
                        ->label('Volume Required (m³)')
                        ->numeric()
                        ->required()
                        ->minValue(0.001)
                        ->step(0.001)
                        ->suffix('m³')
                        ->placeholder('e.g. 1.5'),
                    ])->columns(2)
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
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('volume_required')
                            ->label('Volume Required (m³)')
                            ->numeric()
                            ->required()
                            ->minValue(0.001)
                            ->step(0.001)
                            ->suffix('m³'),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
                // DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
