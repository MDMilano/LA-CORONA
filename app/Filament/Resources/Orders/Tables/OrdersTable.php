<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable()
                    ->money(currency: 'php')
                    ->summarize([
                        Sum::make()
                            ->label('Sum')
                            ->money(currency: 'php'),
                    ]),
                TextColumn::make('status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'pending',
                        'success' => 'completed',
                        'primary' => 'processing',
                    ]),
                TextColumn::make('delivery_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('processing')
                        ->label('Processing')
                        ->icon(Heroicon::OutlinedPlay)
                        ->color('primary')
                        ->visible(fn (Order $record) => $record->status === 'pending')
                        ->action(function (Order $record) {
                            $record->status = 'processing';
                            $record->save();
                        }),
                    Action::make('complete')
                        ->label('Complete')
                        ->icon(Heroicon::OutlinedCheck)
                        ->color('success')
                        ->visible(fn (Order $record) => $record->status === 'processing' || $record->status === 'pending')
                        ->action(function (Order $record) {
                            $record->status = 'completed';
                            $record->save();
                        }),
                    Action::make('cancel')
                        ->label('Cancel')
                        ->icon(Heroicon::OutlinedXMark)
                        ->color('danger')
                        ->visible(fn (Order $record) => $record->status !== 'cancelled' && $record->status !== 'completed' && $record->status !== 'processing')
                        ->action(function (Order $record) {
                            $record->status = 'cancelled';
                            $record->save();
                        }),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
