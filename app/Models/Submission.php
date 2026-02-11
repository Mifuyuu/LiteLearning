<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'content',
        'status',
        'score',
        'feedback',
        'turned_in_at',
        'graded_at',
    ];

    protected $casts = [
        'turned_in_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function quizResponses()
    {
        return $this->hasMany(QuizResponse::class);
    }

    // Helpers
    public function isTurnedIn(): bool
    {
        return in_array($this->status, ['turned_in', 'graded', 'returned']);
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    public function turnIn(): void
    {
        $this->update([
            'status' => 'turned_in',
            'turned_in_at' => now(),
        ]);
    }

    public function unsubmit(): void
    {
        $this->update([
            'status' => 'assigned',
            'turned_in_at' => null,
        ]);
    }

    public function grade(int $score, ?string $feedback = null): void
    {
        $this->update([
            'score' => $score,
            'feedback' => $feedback,
            'status' => 'graded',
            'graded_at' => now(),
        ]);
    }

    public function returnSubmission(): void
    {
        $this->update([
            'status' => 'returned',
        ]);
    }
}
