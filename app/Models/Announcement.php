<?php

namespace App\Models;

use App\Models\Traits\HasCommentsAndAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasCommentsAndAttachments, HasFactory;

    protected $fillable = [
        'classwork_item_id',
        'content',
    ];

    public function classworkItem(): BelongsTo
    {
        return $this->belongsTo(ClassworkItem::class);
    }

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

    public function getClassroomAttribute(): ?Classroom
    {
        return $this->classworkItem?->classroom;
    }

    public function getUserAttribute(): ?User
    {
        return $this->classworkItem?->user;
    }
}
