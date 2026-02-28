<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ClassroomContent extends Model
{
    public $incrementing = false;

    // No single-column PK — composite PK is (classroom_id, contentable_type, contentable_id)
    // We override delete() so Eloquent doesn't try WHERE id = ?
    protected $primaryKey = null;

    protected $fillable = [
        'classroom_id',
        'contentable_type',
        'contentable_id',
        'order',
        'pinned_at',
    ];

    protected function casts(): array
    {
        return [
            'pinned_at' => 'datetime',
        ];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function contentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Override delete to use composite PK (no id column on this table).
     */
    public function delete(): bool|null
    {
        static::query()
            ->where('classroom_id', $this->classroom_id)
            ->where('contentable_type', $this->contentable_type)
            ->where('contentable_id', $this->contentable_id)
            ->delete();

        return true;
    }
}
