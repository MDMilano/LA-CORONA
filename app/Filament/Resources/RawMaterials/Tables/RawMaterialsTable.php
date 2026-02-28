<?php

namespace App\Filament\Resources\RawMaterials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RawMaterialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('current_stock')
                    ->numeric()
                    ->sortable()
                    ->suffix('m³')
                    ->color(function (TextColumn $column) {
                        return match (true) {
                            $column->getState() <= 10 => 'danger',
                            $column->getState() <= 50 => 'warning',
                            default => 'success',
                        };
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
