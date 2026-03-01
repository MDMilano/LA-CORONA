<?php

namespace App\Filament\Resources\RawMaterials\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
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
                    ->badge()
                    ->suffix('m³')
                    ->color(function (TextColumn $column) {
                        return match (true) {
                            $column->getState() <= 10 => 'danger',
                            $column->getState() <= 50 => 'warning',
                            default => 'success',
                        };
                    }),
                TextColumn::make('created_at')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('adjust_stock')
                    ->label('Adjust Stock')
                    ->icon('heroicon-m-cube-transparent')
                    ->modalWidth('xs')
                    ->form([
                        TextInput::make('new_stock')
                            ->label('New Stock (m³)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('m³')
                            ->default(fn ($record) => $record->current_stock),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['current_stock' => $data['new_stock']]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
