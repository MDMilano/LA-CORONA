<?php

namespace App\Filament\Resources\MixerTrucks\Pages;

use App\Filament\Resources\MixerTrucks\MixerTruckResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMixerTruck extends EditRecord
{
    protected static string $resource = MixerTruckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
