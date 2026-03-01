<?php

namespace App\Filament\Resources\RawMaterials\Widgets;

use App\Models\RawMaterial;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RawMaterialStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Raw Materials', RawMaterial::count())
                ->description('Total number of raw materials')
                ->icon('heroicon-m-cube-transparent')
                ->color('primary'),
            Stat::make('Low Stock', RawMaterial::where('current_stock', '<=', 10)->count())
                ->description('Number of raw materials with low stock')
                ->icon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
