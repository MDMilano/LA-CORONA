<?php

namespace App\Filament\Resources\MixerTrucks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MixerTrucksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('capacity')
                    ->label('Capacity (m³)')
                    ->numeric()
                    ->sortable()
                    ->suffix(' m³'),
                TextColumn::make('created_at')
                    ->date('M d, Y h:i A')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->date('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
