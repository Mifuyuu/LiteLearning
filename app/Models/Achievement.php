<?php

namespace App\Models;

use App\Models\Pivots\UserAchievementPivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'coin_reward',
        'xp_reward',
        'is_active',
        'target_role',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->using(UserAchievementPivot::class)
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
}
