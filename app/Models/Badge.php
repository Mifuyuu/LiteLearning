<?php

namespace App\Models;

use App\Models\Pivots\UserBadgePivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'color',
        'target_role',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->using(UserBadgePivot::class)
            ->withPivot('earned_at')
            ->withTimestamps();
    }
}
