<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserBadgePivot extends Pivot
{
    public $incrementing = false;

    protected $primaryKey = null;

    protected $table = 'user_badges';

    protected $fillable = [
        'user_id',
        'badge_id',
        'earned_at',
    ];

    protected function casts(): array
    {
        return [
            'earned_at' => 'datetime',
        ];
    }
}
