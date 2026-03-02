<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MixerTruck extends Model
{
    protected $fillable = [
        'name',
        'capacity',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
