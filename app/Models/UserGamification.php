<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGamification extends Model
{
    protected $fillable = [
        'user_id',
        'coins',
        'xp',
        'level',
    ];

    protected $casts = [
        'coins' => 'integer',
        'xp' => 'integer',
        'level' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
