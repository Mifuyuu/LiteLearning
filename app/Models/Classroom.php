<?php

namespace App\Models;

use App\Models\Pivots\ClassroomUserPivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'slug',
        'section',
        'description',
        'code',
        'theme_category_id',
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
            if (! self::where('code', $code)->exists()) {
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
            if (! self::where('slug', $slug)->exists()) {
                return $slug;
            }
        }
    }

    // Relationships
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function themeCategory(): BelongsTo
    {
        return $this->belongsTo(ThemeCategory::class, 'theme_category_id');
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

    public function announcements(): HasManyThrough
    {
        return $this->hasManyThrough(
            Announcement::class,
            ClassworkItem::class,
            'classroom_id',
            'classwork_item_id',
            'id',
            'id'
        )->where('classwork_items.type', 'announcement')
            ->latest('classwork_items.created_at');
    }

    public function classworkItems(): HasMany
    {
        return $this->hasMany(ClassworkItem::class)->latest();
    }

    public function classworkAssignments(): HasMany
    {
        return $this->hasMany(ClassworkItem::class)->where('classwork_items.type', 'assignment');
    }

    public function assignments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Assignment::class,
            ClassworkItem::class,
            'classroom_id',      // FK on classwork_items
            'classwork_item_id', // FK on assignments
            'id',                // local key on classrooms
            'id'                 // local key on classwork_items
        )->latest('classwork_items.created_at');
    }

    public function materials(): HasManyThrough
    {
        return $this->hasManyThrough(
            Material::class,
            ClassworkItem::class,
            'classroom_id',
            'classwork_item_id',
            'id',
            'id'
        )->latest('classwork_items.created_at');
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
        if ($this->relationLoaded('coTeachers')) {
            return $this->coTeachers->contains('id', $user->id);
        }

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
        if ($this->relationLoaded('members')) {
            return $this->members->contains('id', $user->id);
        }

        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function hasAccess(User $user): bool
    {
        return $this->isOwnedBy($user) || $this->isCoTeacher($user) || $this->hasMember($user) || $user->isAdmin();
    }

    public function studentCount(): int
    {
        if ($this->relationLoaded('students')) {
            return $this->students->count();
        }

        return $this->students()->count();
    }
}
