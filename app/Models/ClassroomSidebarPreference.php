<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassroomSidebarPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'classroom_id',
        'is_pinned',
        'position',
        'pinned_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'position' => 'integer',
        'pinned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
