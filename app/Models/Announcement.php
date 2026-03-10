<?php

namespace App\Models;

use App\Models\Traits\BelongsToClassroom;
use App\Models\Traits\HasCommentsAndAttachments;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use BelongsToClassroom, HasCommentsAndAttachments, HasFactory, HasSlug;

    protected $fillable = [
        'user_id',
        'classroom_id',
        'title',
        'slug',
        'content',
    ];
}
