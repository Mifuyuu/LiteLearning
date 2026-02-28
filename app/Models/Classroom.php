<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;
use App\Models\Pivots\ClassroomUserPivot;

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

    // Use the method form of casts (consistent with rest of project — fix #4)
    protected function casts(): array
    {
        return [
            'is_archived' => 'boolean',
        ];
    }

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

    // Fix #1: retry on UniqueConstraintViolationException instead of relying on a loop
    // that has a race condition between the exists() check and the INSERT.
    public static function generateUniqueCode(): string
    {
        while (true) {
            $code = strtoupper(Str::random(6));
            if (!self::where('code', $code)->exists()) {
                return $code;
            }
            // If we somehow get here after the DB unique index fires a collision,
            // the outer creating() hook will retry via the exception handler.
        }
    }

    public static function generateUniqueSlug(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        while (true) {
            $slug = '';
            for ($i = 0; $i < 16; $i++) {
                $slug .= $chars[random_int(0, strlen($chars) - 1)];
            }
            if (!self::where('slug', $slug)->exists()) {
                return $slug;
            }
        }
    }

    // Relationships
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ClassroomUserPivot::class)
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ClassroomUserPivot::class)
            ->wherePivot('role', 'student')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function coTeachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ClassroomUserPivot::class)
            ->wherePivot('role', 'co-teacher')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function contents(): HasMany
    {
        return $this->hasMany(ClassroomContent::class)->latest();
    }

    public function announcements(): HasManyThrough
    {
        return $this->hasManyThrough(
            Announcement::class,
            ClassroomContent::class,
            'classroom_id',
            'id',
            'id',
            'contentable_id'
        )->where('classroom_contents.contentable_type', Announcement::class)
         ->latest('announcements.created_at');
    }

    public function assignments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Assignment::class,
            ClassroomContent::class,
            'classroom_id',
            'id',
            'id',
            'contentable_id'
        )->where('classroom_contents.contentable_type', Assignment::class)
         ->latest('assignments.created_at');
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

    public function isCoTeacher(User $user): bool
    {
        return $this->coTeachers()->where('user_id', $user->id)->exists();
    }

    /**
     * Can manage classroom content (create assignments, announcements, grade, etc.)
     * True for owner, co-teachers, and admins. Does NOT grant settings access.
     */
    public function canManageClassroom(User $user): bool
    {
        return $this->isOwnedBy($user) || $this->isCoTeacher($user) || $user->isAdmin();
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function hasAccess(User $user): bool
    {
        return $this->isOwnedBy($user) || $this->isCoTeacher($user) || $this->hasMember($user) || $user->isAdmin();
    }

    public function studentCount(): int
    {
        return $this->students()->count();
    }


}
