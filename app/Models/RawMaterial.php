<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterial extends Model
{
    protected $fillable = ['name', 'unit', 'current_stock'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_raw_materials')
            ->withPivot('volume_required')
            ->withTimestamps();
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
