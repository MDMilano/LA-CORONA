<?php

namespace App\Filament\Widgets;

use App\Models\RawMaterial;
use Filament\Widgets\ChartWidget;

class RawMaterialsChart extends ChartWidget
{
    protected ?string $heading = 'Current Raw Materials Inventory';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Fetch all raw materials from the database
        $materials = RawMaterial::all();

        return [
            'datasets' => [
                [
                    'label' => 'Current Stock (m³)',
                    // Pluck the specific columns to populate the chart data
                    'data' => $materials->pluck('current_stock')->toArray(),
                    
                    // Add some colors for the bars
                    'backgroundColor' => ['#f59e0b', '#3b82f6', '#10b981', '#6366f1'], 
                ],
            ],
            // Use the material names (Sand, Gravel, Cement) for the bottom labels
            'labels' => $materials->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
