<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Widgets\OrderStats;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->query(fn (Builder $query) => $query),
            'pending' => Tab::make('Pending')
                ->query(fn (Builder $query) => $query->where('status', 'pending')),
            'processing' => Tab::make('Processing')
                ->query(fn (Builder $query) => $query->where('status', 'processing')),
            'completed' => Tab::make('Completed')
                ->query(fn (Builder $query) => $query->where('status', 'completed')),
            'cancelled' => Tab::make('Cancelled')
                ->query(fn (Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}
