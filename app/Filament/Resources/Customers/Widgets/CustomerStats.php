<?php

namespace App\Filament\Resources\Customers\Widgets;

use App\Models\Customer;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Customer::count())
                ->description('Total number of customers')
                ->icon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Active Customers', Customer::whereHas('orders', function ($query) {
                    $query->where('created_at', '>=', now()->subMonth());
                })->count())
                ->description('Number of active customers ')
                ->icon('heroicon-m-user-plus')
                ->color('success'),
            Stat::make('Inactive Customers', Customer::whereDoesntHave('orders', function ($query) {
                    $query->where('created_at', '>=', now()->subMonth());
                })->count())
                ->description('Number of inactive customers')
                ->icon('heroicon-m-user-minus')
                ->color('danger'),
        ];
    }
}
