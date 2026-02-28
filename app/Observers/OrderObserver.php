<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->isDirty('status') && $order->status === 'completed') {

            DB::transaction(function () use ($order) {
                // Get the recipe for the ordered product
                $ingredients = $order->product->rawMaterials;

                foreach ($ingredients as $ingredient) {
                    // Calculate total m³ needed: (m³ per product) * (quantity of products ordered)
                    $totalVolumeNeeded = $ingredient->pivot->volume_required * $order->quantity;

                    // Deduct from current stock
                    $ingredient->decrement('current_stock', $totalVolumeNeeded);
                }
            });
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
