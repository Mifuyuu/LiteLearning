<?php

namespace App\Models\Traits;

use App\Models\Attachment;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCommentsAndAttachments
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
