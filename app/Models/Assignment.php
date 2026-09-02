<?php

namespace App\Models;

use App\Models\Traits\HasCommentsAndAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assignment extends Model
{
    use HasCommentsAndAttachments, HasFactory;

    protected $fillable = [
        'classwork_item_id',
        'exp_reward',
        'coin_reward',
        'due_date',
        'status',
        'type',
        'allow_late_submission',
        'max_score',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'allow_late_submission' => 'boolean',
            'exp_reward' => 'integer',
            'coin_reward' => 'integer',
            'max_score' => 'integer',
        ];
    }

    // ──────────────────────────────────────────────
    // Route model binding via classwork_items.slug
    // ──────────────────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getRouteKey(): string|int
    {
        return $this->classworkItem?->slug ?? $this->getKey();
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if ($field === 'slug' || $field === null) {
            return static::whereHas('classworkItem', fn ($q) => $q->where('slug', $value))->first()
                ?? (is_numeric($value) ? static::find($value) : null);
        }

        return static::where($field, $value)->first();
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function classworkItem(): BelongsTo
    {
        return $this->belongsTo(ClassworkItem::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function attendanceSession(): HasOne
    {
        return $this->hasOne(AttendanceSession::class, 'classwork_item_id', 'classwork_item_id');
    }

    // ──────────────────────────────────────────────
    // Proxy accessors for shared fields (CTI pattern)
    // ──────────────────────────────────────────────

    public function getClassroomIdAttribute(): ?int
    {
        return $this->classworkItem?->classroom_id;
    }

    public function getUserIdAttribute(): ?int
    {
        return $this->classworkItem?->user_id;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->classworkItem?->title;
    }

    public function getSlugAttribute(): ?string
    {
        return $this->classworkItem?->slug;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->classworkItem?->description;
    }

    /**
     * topic accessor: returns the topic name from the related Topic model via classwork_items.
     */
    public function getTopicAttribute(): ?string
    {
        return $this->classworkItem?->topic?->name;
    }

    /**
     * topic_id accessor for completeness.
     */
    public function getTopicIdAttribute(): ?int
    {
        return $this->classworkItem?->topic_id;
    }

    public function getClassroomAttribute(): ?Classroom
    {
        return $this->classworkItem?->classroom;
    }

    public function getUserAttribute(): ?User
    {
        return $this->classworkItem?->user;
    }

    // ──────────────────────────────────────────────
    // Type helpers
    // ──────────────────────────────────────────────

    public function isAttendance(): bool
    {
        return $this->type === 'attendance';
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    public function isAnnouncement(): bool
    {
        return $this->type === 'announcement';
    }

    public function isTopic(): bool
    {
        return $this->type === 'topic';
    }

    public function isMaterial(): bool
    {
        return $this->type === 'material';
    }

    /**
     * Get badge color classes for this assignment type.
     * Returns [bg, text] Tailwind classes.
     */
    public function typeColor(): array
    {
        return match ($this->type) {
            'attendance' => ['bg-amber-100', 'text-amber-700'],
            'file' => ['bg-blue-100', 'text-blue-700'],
            'topic' => ['bg-cyan-100', 'text-cyan-700'],
            'material' => ['bg-slate-100', 'text-slate-700'],
            'announcement' => ['bg-orange-100', 'text-orange-700'],
            default => ['bg-gray-100', 'text-gray-700'],
        };
    }

    /**
     * Get the translation key for this assignment type.
     */
    public function typeLabel(): string
    {
        return match ($this->type) {
            'attendance' => __('app.type_attendance'),
            'file' => __('app.type_file'),
            'topic' => __('app.type_topic'),
            'material' => __('app.type_material'),
            'announcement' => __('app.type_announcement'),
            default => ucfirst($this->type),
        };
    }

    // ──────────────────────────────────────────────
    // Due-date / Overdue helpers
    // ──────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Human-readable description of how overdue the assignment is.
     * Returns null if not overdue.
     */
    public function overdueDescription(): ?string
    {
        if (! $this->isOverdue()) {
            return null;
        }

        $diff = $this->due_date->diff(now());

        if ($diff->days >= 1) {
            return __('app.overdue_days', ['days' => $diff->days, 'hours' => $diff->h]);
        }

        if ($diff->h >= 1) {
            return __('app.overdue_hours', ['hours' => $diff->h, 'minutes' => $diff->i]);
        }

        return __('app.overdue_minutes', ['minutes' => max(1, $diff->i)]);
    }

    /**
     * Whether this assignment can accept late submissions.
     */
    public function canSubmitLate(): bool
    {
        return $this->allow_late_submission;
    }

    /**
     * Whether a submission can currently be accepted.
     */
    public function canAcceptSubmission(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if (! $this->isOverdue()) {
            return true;
        }

        return $this->canSubmitLate();
    }

    /**
     * Whether this type requires submission (not material).
     */
    public function requiresSubmission(): bool
    {
        return ! in_array($this->type, ['material', 'announcement', 'topic']);
    }

    // ──────────────────────────────────────────────
    // Submission queries
    // ──────────────────────────────────────────────

    public function submissionFor(User $user): ?Submission
    {
        return $this->submissions()->where('user_id', $user->id)->first();
    }

    public function submittedCount(): int
    {
        if ($this->relationLoaded('submissions')) {
            return $this->submissions->filter(fn ($submission) => in_array($submission->status, ['turned_in', 'graded'], true))->count();
        }

        return $this->submissions()->whereIn('status', ['turned_in', 'graded'])->count();
    }

    public function gradedCount(): int
    {
        if ($this->relationLoaded('submissions')) {
            return $this->submissions->where('status', 'graded')->count();
        }

        return $this->submissions()->where('status', 'graded')->count();
    }

    public function averageScore(): ?float
    {
        if ($this->relationLoaded('submissions')) {
            $graded = $this->submissions->where('status', 'graded');

            return $graded->isNotEmpty() ? (float) $graded->avg('score') : null;
        }

        return $this->submissions()->where('status', 'graded')->avg('score');
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    public function scopeOfType($query, string $type)
    {
        return $query->where('assignments.type', $type);
    }

    public function scopePublished($query)
    {
        return $query->where('assignments.status', 'published');
    }

    // Fix #5: Allow only safe file types for student submission uploads.
    public static function allowedSubmissionMimes(): string
    {
        return 'pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,webp,zip,rar,txt,csv';
    }
}
