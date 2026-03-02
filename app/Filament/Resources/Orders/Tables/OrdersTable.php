<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Resources\RawMaterials\RawMaterialResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
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
                    ->label('Concrete Class')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('mixerTruck.name')
                    ->label('Mixer Truck')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Quantity (trucks)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_volume')
                    ->label('Total Volume (m³)')
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
                            // 1. Load the product and its recipe
                            $product = $record->product()->with('rawMaterials')->first();
                            $shortages = [];

                            // 2. Calculate if we have enough stock for this specific order
                            if ($product) {
                                foreach ($product->rawMaterials as $material) {
                                    $needed = $material->pivot->volume_required * $record->total_volume;
                                    if ($needed > $material->current_stock) {
                                        $shortages[] = "<b>{$material->name}:</b> Need {$needed} m³ (Have: {$material->current_stock} m³)";
                                    }
                                }
                            }

                            // 3. If there are shortages, halt the process and show an error!
                            if (!empty($shortages)) {
                                Notification::make()
                                    ->danger()
                                    ->title('Cannot Complete Order')
                                    ->body('Insufficient raw materials:<br><br>' . implode('<br>', $shortages))
                                    ->persistent()
                                    ->actions([
                                        Action::make('update_stock')
                                            ->label('Update Inventory')
                                            ->button()
                                            ->icon(Heroicon::OutlinedPencilSquare)
                                            ->url(RawMaterialResource::getUrl('index'), shouldOpenInNewTab: true),
                                    ])
                                    ->send();
                                
                                return; // This stops the code from saving the record!
                            }

                            // 4. If stock is sufficient, proceed with completing the order
                            $record->status = 'completed';
                            $record->save();

                            // Optional: Show a success message
                            Notification::make()
                                ->success()
                                ->title('Order Completed')
                                ->body('Raw materials have been automatically deducted.')
                                ->send();
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
