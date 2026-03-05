<?php

namespace App\Filament\Resources\MixerTrucks\Widgets;

use App\Models\MixerTruck;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Foundation\Mix;

class MixerTruckStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Mixer Trucks', MixerTruck::count())
                ->description('Total number of mixer trucks')
                ->icon('heroicon-o-truck')
                ->color('info'),
        ];
    }
}
