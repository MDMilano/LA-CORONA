<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueTrendChart extends ChartWidget
{
    protected ?string $heading = '7-Day Revenue Trend';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Loop backwards through the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            // Format the label (e.g., "Mar 02")
            $labels[] = $date->format('M d');

            // Sum the total_amount of non-cancelled orders for that specific date
            $dailyRevenue = Order::whereDate('created_at', $date)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');

            $data[] = $dailyRevenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (₱)',
                    'data' => $data,
                    'borderColor' => '#10b981', // A nice green line for money
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
