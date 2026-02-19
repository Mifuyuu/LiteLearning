<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'user_id',
        'title',
        'slug',
        'description',
        'attachments',
        'max_score',
        'due_date',
        'status',
        'type',
        'topic',
        'allow_late_submission',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'due_date' => 'datetime',
            'allow_late_submission' => 'boolean',
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

    protected $casts = [
        'due_date' => 'datetime',
        'allow_late_submission' => 'boolean',
    ];

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

    public function quizQuestions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
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

    public function isQuiz(): bool
    {
        return $this->type === 'quiz';
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
            'attendance' => 'fa-clipboard-check',
            'file' => 'fa-cloud-arrow-up',
            'question' => 'fa-pen-to-square',
            'quiz' => 'fa-circle-question',
            'material' => 'fa-book-open',
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
            'quiz' => ['bg-purple-100', 'text-purple-700'],
            'material' => ['bg-slate-100', 'text-slate-700'],
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
            'quiz' => __('Quiz'),
            'material' => __('Material'),
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
        if (!$this->isOverdue()) {
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
        if (!$this->isOverdue()) {
            return true;
        }

        return $this->canSubmitLate();
    }

    /**
     * Whether this type requires submission (not material).
     */
    public function requiresSubmission(): bool
    {
        return !$this->isMaterial();
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
}
