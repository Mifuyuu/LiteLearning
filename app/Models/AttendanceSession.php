<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSession extends Model
{
    use HasFactory;

    public const CODE_ROTATION_SECONDS = 10;

    // Matches rotation: a code is only ever valid for the window it's on screen for,
    // like a TOTP — no grace period past rotation.
    public const CODE_VALIDITY_SECONDS = 10;

    protected $fillable = [
        'classwork_item_id',
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

    public function classworkItem(): BelongsTo
    {
        return $this->belongsTo(ClassworkItem::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'classwork_item_id', 'classwork_item_id');
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
     * Check if the current code has expired.
     */
    public function isCodeExpired(): bool
    {
        if (! $this->code_rotated_at) {
            return true;
        }

        return $this->code_rotated_at->diffInSeconds(now()) >= self::CODE_VALIDITY_SECONDS;
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
