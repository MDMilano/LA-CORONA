<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;

class TodayOrdersTable extends TableWidget
{
    // 1. Placement Settings for the Dashboard
    protected static ?int $sort = 3; 
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = "Today's Dispatch Board";

    public function table(Table $table): Table
    {
        return $table
            // 2. The Query: Only show today's orders that are pending or processing
            ->query(fn (): Builder => Order::query()
                ->whereDate('delivery_date', Carbon::today())
                ->whereIn('status', ['pending', 'processing'])
                ->orderBy('created_at', 'asc')
            )
            ->columns([
                TextColumn::make('order_number')->searchable(),
                TextColumn::make('customer.name')->label('Customer'),
                TextColumn::make('product.name')->label('Class'),
                TextColumn::make('mixerTruck.name')->label('Truck'),
                TextColumn::make('total_volume')->label('Volume')->suffix(' m³'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                    ]),
            ])
            ->filters([
                // We don't really need filters here since the query is already tight
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                // 3. Action: Mark as Processing (Dispatch)
                Action::make('dispatch')
                    ->label('Dispatch')
                    ->button() 
                    ->color('primary')
                    ->icon('heroicon-o-truck')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'processing']);
                        
                        Notification::make()
                            ->success()
                            ->title('Truck Dispatched')
                            ->send();
                    }),

                // 4. Action: Mark as Complete (With Inventory Validation)
                Action::make('complete')
                    ->label('Complete')
                    ->button()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Order $record) => $record->status === 'processing')
                    ->action(function (Order $record) {
                        // Load the product and its raw materials recipe
                        $product = $record->product()->with('rawMaterials')->first();
                        $shortages = [];

                        if ($product) {
                            foreach ($product->rawMaterials as $material) {
                                $needed = $material->pivot->volume_required * $record->total_volume;
                                if ($needed > $material->current_stock) {
                                    $shortages[] = "<b>{$material->name}:</b> Need {$needed} m³ (Have: {$material->current_stock} m³)";
                                }
                            }
                        }

                        // Prevent completion if there isn't enough inventory
                        if (!empty($shortages)) {
                            Notification::make()
                                ->danger()
                                ->title('Cannot Complete Order')
                                ->body('Insufficient raw materials:<br><br>' . implode('<br>', $shortages))
                                ->persistent()
                                ->send();
                            return; 
                        }

                        // If safe, mark as completed
                        $record->update(['status' => 'completed']);

                        Notification::make()
                            ->success()
                            ->title('Order Completed')
                            ->body('Raw materials have been deducted.')
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}