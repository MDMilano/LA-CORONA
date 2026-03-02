<?php

namespace App\Filament\Resources\MixerTrucks;

use App\Filament\Resources\MixerTrucks\Pages\CreateMixerTruck;
use App\Filament\Resources\MixerTrucks\Pages\EditMixerTruck;
use App\Filament\Resources\MixerTrucks\Pages\ListMixerTrucks;
use App\Filament\Resources\MixerTrucks\Schemas\MixerTruckForm;
use App\Filament\Resources\MixerTrucks\Tables\MixerTrucksTable;
use App\Models\MixerTruck;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MixerTruckResource extends Resource
{
    protected static string | UnitEnum | null $navigationGroup = 'Shop Management';

    protected static ?int $navigationSort = 4;
    
    protected static ?string $model = MixerTruck::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    public static function form(Schema $schema): Schema
    {
        return MixerTruckForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MixerTrucksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMixerTrucks::route('/'),
            'create' => CreateMixerTruck::route('/create'),
            'edit' => EditMixerTruck::route('/{record}/edit'),
        ];
    }
}
