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
        'pending_celebrations',
    ];

    protected function casts(): array
    {
        return [
            'coins' => 'integer',
            'xp' => 'integer',
            'level' => 'integer',
            'pending_celebrations' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
