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
                // Get the 1m³ recipe for the ordered Concrete Class
                $ingredients = $order->product->rawMaterials;
                
                foreach ($ingredients as $ingredient) {
                    // The math: (Amount needed for 1 m³) * (Total m³ ordered)
                    // e.g., 0.5m³ Sand * 18m³ Total Volume = 9m³ Sand needed
                    $totalMaterialNeeded = $ingredient->pivot->volume_required * $order->total_volume;
                    
                    // Deduct from current stock
                    $ingredient->decrement('current_stock', $totalMaterialNeeded);
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
