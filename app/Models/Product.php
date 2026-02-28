<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'description'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function rawMaterials(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class , 'product_raw_materials')
            ->withPivot('volume_required')
            ->withTimestamps();
    }
}
