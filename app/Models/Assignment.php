<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\UniqueConstraintViolationException;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'classroom_id',
        'exp_reward',
        'coin_reward',
        'due_date',
        'status',
        'type',
        'topic',
        'allow_late_submission',
        'max_score',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'due_date' => 'datetime',
            'allow_late_submission' => 'boolean',
            'exp_reward' => 'integer',
            'coin_reward' => 'integer',
            'max_score' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Assignment $assignment) {
            if (empty($assignment->slug)) {
                $assignment->slug = self::generateUniqueSlug();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Generate a unique 16-char random slug.
     * The DB unique index on `assignments.slug` acts as the final race-condition guard;
     * if two processes generate the same slug simultaneously, the second INSERT will
     * throw UniqueConstraintViolationException which should be handled by the caller.
     */
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

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function attendanceSession(): HasOne
    {
        return $this->hasOne(AttendanceSession::class);
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

    public function isQuestion(): bool
    {
        return $this->type === 'question';
    }

    public function isAnnouncement(): bool
    {
        return $this->type === 'announcement';
    }

    public function isTopic(): bool
    {
        return $this->type === 'topic';
    }

    public function isProject(): bool
    {
        return $this->type === 'project';
    }

    public function isMaterial(): bool
    {
        return $this->type === 'material';
    }

    /**
     * Get the FontAwesome icon class for this assignment type.
     */
    public function typeIcon(): string
    {
        return match ($this->type) {
            'announcement' => 'fa-bullhorn',
            'attendance' => 'fa-clipboard-check',
            'file' => 'fa-cloud-arrow-up',
            'question' => 'fa-pen-to-square',
            'topic' => 'fa-layer-group',
            'material' => 'fa-book-open',
            'project' => 'fa-diagram-project',
            default => 'fa-file-alt',
        };
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
            'question' => ['bg-green-100', 'text-green-700'],
            'topic' => ['bg-cyan-100', 'text-cyan-700'],
            'material' => ['bg-slate-100', 'text-slate-700'],
            'announcement' => ['bg-orange-100', 'text-orange-700'],
            'project' => ['bg-rose-100', 'text-rose-700'],
            default => ['bg-gray-100', 'text-gray-700'],
        };
    }

    /**
     * Get the translation key for this assignment type.
     */
    public function typeLabel(): string
    {
        return match ($this->type) {
            'attendance' => __('Attendance'),
            'file' => __('File Upload'),
            'question' => __('Question'),
            'topic' => __('Topic'),
            'material' => __('Material'),
            'announcement' => __('Announcement'),
            'project' => __('Project'),
            default => __(ucfirst($this->type)),
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
            return __('Overdue by :days days :hours hours', [
                'days' => $diff->days,
                'hours' => $diff->h,
            ]);
        }

        if ($diff->h >= 1) {
            return __('Overdue by :hours hours :minutes minutes', [
                'hours' => $diff->h,
                'minutes' => $diff->i,
            ]);
        }

        return __('Overdue by :minutes minutes', [
            'minutes' => max(1, $diff->i),
        ]);
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
        return $this->submissions()->whereIn('status', ['turned_in', 'graded', 'returned'])->count();
    }

    public function gradedCount(): int
    {
        return $this->submissions()->where('status', 'graded')->count();
    }

    public function averageScore(): ?float
    {
        return $this->submissions()->where('status', 'graded')->avg('score');
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Fix #5: Allow only safe file types for student submission uploads.
    public static function allowedSubmissionMimes(): string
    {
        return 'pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,webp,zip,rar,txt,csv';
    }
}
