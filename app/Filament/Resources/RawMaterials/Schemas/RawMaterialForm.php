<?php

namespace App\Filament\Resources\RawMaterials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RawMaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Raw Material Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('current_stock')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.001)
                            ->suffix('m³'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
