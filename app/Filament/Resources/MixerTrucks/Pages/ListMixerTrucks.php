<?php

namespace App\Filament\Resources\MixerTrucks\Pages;

use App\Filament\Resources\MixerTrucks\MixerTruckResource;
use App\Filament\Resources\MixerTrucks\Widgets\MixerTruckStats;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMixerTrucks extends ListRecords
{
    protected static string $resource = MixerTruckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MixerTruckStats::class,
        ];
    }
}
