<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count())
                ->description('Total number of orders')
                ->icon('heroicon-m-shopping-cart')
                ->color('primary'),
            Stat::make('Pending Orders', Order::where('status', 'pending')->count())
                ->description('Number of pending orders')
                ->icon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Processing Orders', Order::where('status', 'processing')->count())
                ->description('Number of processing orders')
                ->icon('heroicon-m-cog')
                ->color('info'),
            Stat::make('Completed Orders', Order::where('status', 'completed')->count())
                ->description('Number of completed orders')
                ->icon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Cancelled Orders', Order::where('status', 'cancelled')->count())
                ->description('Number of cancelled orders')
                ->icon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
