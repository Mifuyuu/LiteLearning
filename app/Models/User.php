<?php

namespace App\Models;

use App\Models\Pivots\ClassroomUserPivot;
use App\Models\Pivots\UserAchievementPivot;
use App\Models\Pivots\UserStoreItemPivot;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'cover_image',
        'bio',
        'is_active',
    ];

    public function fill(array $attributes)
    {
        if ($this->exists && array_key_exists('role', $attributes)) {
            throw new \LogicException('Role cannot be changed via fill() on an existing user. Use $user->role = \'...\' directly for explicit role changes.');
        }

        return parent::fill($attributes);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',

            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function gamification(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserGamification::class);
    }

    public function ownedClassrooms(): HasMany
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    public function enrolledClassrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class)
            ->using(ClassroomUserPivot::class)
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function assignments(): HasManyThrough
    {
        return $this->hasManyThrough(Assignment::class, ClassworkItem::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function coinTransactions(): HasMany
    {
        return $this->hasMany(CoinTransaction::class)->latest();
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->using(UserAchievementPivot::class)
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    public function storeItems(): BelongsToMany
    {
        return $this->belongsToMany(StoreItem::class, 'user_store_items')
            ->using(UserStoreItemPivot::class)
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function bugReports(): HasMany
    {
        return $this->hasMany(BugReport::class);
    }

    // Helpers
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function allClassrooms(): \Illuminate\Support\Collection
    {
        if ($this->isTeacher() || $this->isAdmin()) {
            // Deduplicate by id: a teacher enrolled in their own classroom would appear twice otherwise.
            return $this->ownedClassrooms()->where('is_archived', false)->get()
                ->merge($this->enrolledClassrooms()->where('is_archived', false)->get())
                ->unique('id')
                ->values();
        }

        return $this->enrolledClassrooms()->where('is_archived', false)->get();
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return \Illuminate\Support\Facades\Storage::url($this->avatar);
        }

        return asset('images/default_profile_pic.webp').'?v='.filemtime(public_path('images/default_profile_pic.webp'));
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->cover_image) {
            return \Illuminate\Support\Facades\Storage::url($this->cover_image);
        }

        return null;
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials;
    }

    // Magic getters for gamification downward compatibility
    public function getCoinsAttribute(): int
    {
        return $this->gamification?->coins ?? 0;
    }

    public function getXpAttribute(): int
    {
        return $this->gamification?->xp ?? 0;
    }

    public function getLevelAttribute(): int
    {
        return $this->gamification?->level ?? 1;
    }

    public function getActiveNameColorAttribute(): ?string
    {
        return $this->storeItems()
            ->wherePivot('is_active', true)
            ->where('type', 'name_color')
            ->value('value');
    }

    public function getActiveAvatarFrameAttribute(): ?string
    {
        return $this->storeItems()
            ->wherePivot('is_active', true)
            ->where('type', 'avatar_frame')
            ->value('value');
    }
}
