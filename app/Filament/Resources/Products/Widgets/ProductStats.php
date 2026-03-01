<?php

namespace App\Filament\Resources\Products\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count())
                ->description('Total number of products')
                ->icon('heroicon-m-cube')
                ->color('primary'),
            Stat::make('Active Products', Product::whereHas('orders', function ($query) {
                    $query->where('created_at', '>=', now()->subMonth());
                })->count())
                ->description('Number of active products')
                ->icon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Inactive Products', Product::whereDoesntHave('orders', function ($query) {
                    $query->where('created_at', '>=', now()->subMonth());
                })->count())
                ->description('Number of inactive products')
                ->icon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
