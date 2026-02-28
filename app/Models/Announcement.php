<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
    ];

    public function classroomContent(): MorphOne
    {
        return $this->morphOne(ClassroomContent::class, 'contentable');
    }

    /**
     * Get the classroom this announcement belongs to via the content hub.
     */
    public function getClassroom(): ?Classroom
    {
        return $this->classroomContent?->classroom;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
