<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSession extends Model
{
    protected $fillable = [
        'assignment_id',
        'current_code',
        'is_active',
        'started_at',
        'code_rotated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'started_at' => 'datetime',
            'code_rotated_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Generate a new 6-digit random attendance code.
     */
    public function generateNewCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'current_code' => $code,
            'code_rotated_at' => now(),
        ]);

        return $code;
    }

    /**
     * Check if the current code has expired (older than 10 seconds).
     */
    public function isCodeExpired(): bool
    {
        if (!$this->code_rotated_at) {
            return true;
        }

        return $this->code_rotated_at->diffInSeconds(now()) >= 10;
    }

    /**
     * Start the attendance session.
     */
    public function start(): void
    {
        $this->update([
            'is_active' => true,
            'started_at' => now(),
        ]);

        $this->generateNewCode();
    }

    /**
     * Stop the attendance session.
     */
    public function stop(): void
    {
        $this->update([
            'is_active' => false,
            'current_code' => null,
        ]);
    }
}
