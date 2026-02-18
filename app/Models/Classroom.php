<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'slug',
        'section',
        'subject',
        'description',
        'code',
        'cover_image',
        'theme_color',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Classroom $classroom) {
            if (empty($classroom->code)) {
                $classroom->code = self::generateUniqueCode();
            }
            if (empty($classroom->slug)) {
                $classroom->slug = self::generateUniqueSlug();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public static function generateUniqueSlug(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        do {
            $slug = '';
            for ($i = 0; $i < 16; $i++) {
                $slug .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (self::where('slug', $slug)->exists());

        return $slug;
    }

    // Relationships
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('role', 'student')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class)->latest();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class)->latest();
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class)->orderBy('order');
    }

    // Helpers
    public function isOwnedBy(User $user): bool
    {
        return $this->teacher_id === $user->id;
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function hasAccess(User $user): bool
    {
        return $this->isOwnedBy($user) || $this->hasMember($user) || $user->isAdmin();
    }

    public function studentCount(): int
    {
        return $this->students()->count();
    }

    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }

        $covers = [
            'https://gstatic.com/classroom/themes/img_graduation.jpg',
            'https://gstatic.com/classroom/themes/img_bookclub.jpg',
            'https://gstatic.com/classroom/themes/img_code.jpg',
            'https://gstatic.com/classroom/themes/img_breakfast.jpg',
        ];

        return $covers[$this->id % count($covers)];
    }
}
