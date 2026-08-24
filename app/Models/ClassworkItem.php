<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class ClassworkItem extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (ClassworkItem $item) {
            if (empty($item->slug)) {
                $item->slug = static::generateUniqueSlug();
            }
        });
    }

    public static function generateUniqueSlug(): string
    {
        do {
            $slug = Str::random(16);
        } while (static::where('slug', $slug)->exists());

        return $slug;
    }

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
