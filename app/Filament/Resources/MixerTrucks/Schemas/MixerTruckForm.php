<?php

namespace App\Filament\Resources\MixerTrucks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MixerTruckForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mixer Truck Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Standard 9m³ Truck'),
                        TextInput::make('capacity')
                            ->label('Capacity (m³)')
                            ->numeric()
                            ->required()
                            ->suffix(' m³')
                            ->placeholder('e.g., 9.00'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
