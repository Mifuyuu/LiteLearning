<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGamification extends Model
{
    protected $fillable = [
        'user_id',
        'coins',
        'xp',
        'level',
    ];

    protected function casts(): array
    {
        return [
            'coins' => 'integer',
            'xp' => 'integer',
            'level' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
