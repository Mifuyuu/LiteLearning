<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClassworkItem extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'type',
        'classroom_id',
        'user_id',
        'topic_id',
        'title',
        'slug',
        'description',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function material(): HasOne
    {
        return $this->hasOne(Material::class);
    }

    public function announcement(): HasOne
    {
        return $this->hasOne(Announcement::class);
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(AttendanceSession::class);
    }
}
