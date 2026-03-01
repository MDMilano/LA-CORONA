<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->money(currency: 'php')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('adjust_price')
                    ->label('Adjust Price')
                    ->icon('heroicon-m-currency-dollar')
                    ->modalWidth('xs')
                    ->form([
                        TextInput::make('new_price')
                            ->label('New Price (PHP)')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->prefix('₱')
                            ->default(fn ($record) => $record->price),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['price' => $data['new_price']]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
