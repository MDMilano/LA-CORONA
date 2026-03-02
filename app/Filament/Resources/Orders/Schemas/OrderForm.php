<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Filament\Resources\RawMaterials\RawMaterialResource;
use App\Models\MixerTruck;
use App\Models\Product;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Information')
                    ->schema([
                        Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Section::make('New Customer')
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->required()
                                            ->maxLength(50)
                                            ->prefix('+63'),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                                Section::make()
                                    ->schema([
                                        Textarea::make('address')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->createOptionAction(fn ($action) => $action->visible(fn ($operation) => $operation === 'create')),
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->label('Concrete Class')
                            ->required()
                            ->searchable()
                            ->live()
                            ->preload()
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::updateTotals($set, $get)),
                        Select::make('mixer_truck_id')
                            ->relationship('mixerTruck', 'name')
                            ->label('Mixer Truck')
                            ->required()
                            ->searchable()
                            ->live()
                            ->preload()
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::updateTotals($set, $get)),
                        TextInput::make('quantity')
                            ->label('Number of Trucks')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::updateTotals($set, $get))
                            ->rules([
                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                    $productId = $get('product_id');
                                    $truckId = $get('mixer_truck_id');
                                    
                                    if (!$productId || !$truckId || !$value) return;

                                    $product = Product::with('rawMaterials')->find($productId);
                                    $truck = MixerTruck::find($truckId);
                                    
                                    if (!$product || !$truck) return;

                                    $totalVolume = $truck->capacity * $value;

                                    // Loop through the recipe and check current stocks
                                    foreach ($product->rawMaterials as $material) {
                                        $needed = $material->pivot->volume_required * $totalVolume;
                                        if ($needed > $material->current_stock) {
                                            // This stops the form from submitting and shows the red error text!
                                            $fail("Insufficient {$material->name}. You need {$needed} m³, but only have {$material->current_stock} m³.");
                                        }
                                    }
                                }
                            ]),
                        TextInput::make('total_volume')
                            ->label('Total Volume (m³)')
                            ->numeric()
                            ->required()
                            ->readOnly(true)
                            ->suffix('m³')
                            ->default(0),
                        TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->required()
                            ->readOnly(true)
                            ->prefix('₱')
                            ->default(0),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending')
                            ->disabledOn('edit'),
                        DatePicker::make('delivery_date')
                            ->date()
                            ->required()
                            ->default(now()),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function updateTotals(Set $set, Get $get): void
    {
        $productId = $get('product_id');
        $truckId = $get('mixer_truck_id');
        $quantity = (int) $get('quantity') ?: 1;

        // Only calculate if both a product and a truck have been selected
        if ($productId && $truckId) {
            $product = Product::find($productId);
            $truck = MixerTruck::find($truckId);

            if ($product && $truck) {
                // Calculate Total Volume: Truck Capacity * Quantity
                $totalVolume = $truck->capacity * $quantity;
                
                // Calculate Total Amount: Total Volume * Price per m³
                $totalAmount = $totalVolume * $product->price;

                // Update the form fields automatically
                $set('total_volume', number_format($totalVolume, 2, '.', ''));
                $set('total_amount', number_format($totalAmount, 2, '.', ''));

                $shortages = [];
                foreach ($product->rawMaterials as $material) {
                    $needed = $material->pivot->volume_required * $totalVolume;
                    if ($needed > $material->current_stock) {
                        // Collect all the materials we are short on
                        $shortages[] = "<b>{$material->name}:</b> Need {$needed} m³ (Have: {$material->current_stock} m³)";
                    }

                    if (!empty($shortages)) {
                        Notification::make()
                            ->id('stock_shortage_warning') // This single line prevents multiple popups!
                            ->warning()
                            ->title('Insufficient Raw Materials!')
                            ->body('You do not have enough stock to fulfill this order:<br><br>' . implode('<br>', $shortages))
                            ->persistent() 
                            ->actions([
                                Action::make('update_stock')
                                    ->label('Update Inventory')
                                    ->button()
                                    ->color('warning')
                                    ->url(RawMaterialResource::getUrl('index'), shouldOpenInNewTab: true),
                            ])
                            ->send();
                    } else {
                        Notification::make()
                            ->id('stock_shortage_warning')
                            ->success()
                            ->title('Sufficient Stock')
                            ->body('You have enough raw materials to fulfill this order.')
                            ->send();
                    }
                }
            }
        } else {
            // Reset if they clear a selection
            $set('total_volume', null);
            $set('total_amount', null);
        }
    }
}
