<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreItem extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'price',
        'is_active',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_store_items')
            ->withTimestamps();
    }
}
