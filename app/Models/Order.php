<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'product_id',
        'mixer_truck_id', 'quantity', 'total_volume', 'total_amount', 'status', 'delivery_date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function mixerTruck(): BelongsTo
    {
        return $this->belongsTo(MixerTruck::class);
    }

    // Auto-generate order number
    protected static function booted()
    {
        static::creating(function ($order) {
            $order->order_number = 'ORD-'.strtoupper(uniqid());
                // $order->order_number = 'ORD-' . str_pad(self::count() + 1, 6, '0', STR_PAD_LEFT);
        });
    }
}
