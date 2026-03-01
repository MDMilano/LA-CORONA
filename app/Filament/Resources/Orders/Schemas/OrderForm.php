<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->preload()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $product = \App\Models\Product::find($state);
                                if ($product && $get('quantity')) {
                                    $set('total_amount', $product->price * $get('quantity'));
                                }
                            }),
                        TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $product = \App\Models\Product::find($get('product_id'));
                                if ($product && $state) {
                                    $set('total_amount', $product->price * $state);
                                }
                            }),
                        TextInput::make('total_amount')
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
}
