<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class EmailOtpVerification extends Model
{
    use MassPrunable;

    protected $fillable = [
        'email',
        'otp',
        'user_data',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'user_data'  => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function prunable(): Builder
    {
        return static::where('expires_at', '<', now());
    }
}
