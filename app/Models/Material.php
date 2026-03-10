<?php

namespace App\Models;

use App\Models\Traits\BelongsToClassroom;
use App\Models\Traits\HasCommentsAndAttachments;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use BelongsToClassroom, HasCommentsAndAttachments, HasFactory, HasSlug;

    protected $fillable = [
        'user_id',
        'classroom_id',
        'title',
        'slug',
        'description',
        'topic_id',
    ];

    // ──────────────────────────────────────────────
    // Relationships (Material-specific)
    // ──────────────────────────────────────────────

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
