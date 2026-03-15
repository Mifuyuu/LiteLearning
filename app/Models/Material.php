<?php

namespace App\Models;

use App\Models\Traits\HasCommentsAndAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasCommentsAndAttachments, HasFactory;

    protected $fillable = [
        'classwork_item_id',
    ];

    // ──────────────────────────────────────────────
    // Route model binding via classwork_items.slug
    // ──────────────────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getRouteKey(): string|int
    {
        return $this->classworkItem?->slug ?? $this->getKey();
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if ($field === 'slug' || $field === null) {
            return static::whereHas('classworkItem', fn ($q) => $q->where('slug', $value))->first()
                ?? (is_numeric($value) ? static::find($value) : null);
        }

        return static::where($field, $value)->first();
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function classworkItem(): BelongsTo
    {
        return $this->belongsTo(ClassworkItem::class);
    }

    // ──────────────────────────────────────────────
    // Proxy accessors for shared fields (CTI pattern)
    // ──────────────────────────────────────────────

    public function getClassroomIdAttribute(): ?int
    {
        return $this->classworkItem?->classroom_id;
    }

    public function getUserIdAttribute(): ?int
    {
        return $this->classworkItem?->user_id;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->classworkItem?->title;
    }

    public function getSlugAttribute(): ?string
    {
        return $this->classworkItem?->slug;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->classworkItem?->description;
    }

    public function getTopicIdAttribute(): ?int
    {
        return $this->classworkItem?->topic_id;
    }

    public function getTopicAttribute(): ?string
    {
        return $this->classworkItem?->topic?->name;
    }

    public function getClassroomAttribute(): ?Classroom
    {
        return $this->classworkItem?->classroom;
    }

    public function getUserAttribute(): ?User
    {
        return $this->classworkItem?->user;
    }
}
