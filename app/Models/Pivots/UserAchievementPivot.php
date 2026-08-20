<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserAchievementPivot extends Pivot
{
    public $incrementing = false;

    protected $primaryKey = null;

    protected $table = 'user_achievements';

    protected $fillable = [
        'user_id',
        'achievement_id',
        'unlocked_at',
        'is_displayed',
    ];

    protected function casts(): array
    {
        return [
            'unlocked_at' => 'datetime',
            'is_displayed' => 'boolean',
        ];
    }
}
