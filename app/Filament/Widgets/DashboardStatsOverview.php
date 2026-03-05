<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\RawMaterial;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();
        
        return [
            // 1. Deliveries Today
            Stat::make('Deliveries Today', Order::whereDate('delivery_date', $today)->count())
                ->description('Trucks scheduled for dispatch today')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            // 2. Total Volume to Mix
            Stat::make('Volume to Mix Today', Order::whereDate('delivery_date', $today)->sum('total_volume') . ' m³')
                ->description('Total concrete production needed')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('info'),

            // 3. Expected Revenue
            Stat::make('Expected Revenue', '₱ ' . number_format(Order::whereDate('delivery_date', $today)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'), 2))
                ->description('From today\'s active orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            // 4. Low Stock Alerts
            Stat::make('Low Stock Alerts', RawMaterial::where('current_stock', '<=', 10)->count())
                ->description('Raw materials below 10 m³')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color(RawMaterial::where('current_stock', '<=', 10)->count() > 0 ? 'danger' : 'success'),
        ];
    }
}
