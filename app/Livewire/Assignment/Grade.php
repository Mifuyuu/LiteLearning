<?php

namespace App\Livewire\Assignment;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\ClassworkItem;
use App\Models\Submission;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class Grade extends Component
{
    #[Locked]
    public Classroom $classroom;

    #[Locked]
    public Assignment $assignment;

    #[Locked]
    public Submission $submission;

    public int $score = 0;

    public string $feedback = '';

    public function mount(Classroom $classroom, Assignment $assignment, Submission $submission): void
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $classroom->canManageClassroom($user) && ! $user->isAdmin()) {
            abort(403);
        }

        // Ensure assignment belongs to classroom (prevent IDOR)
        abort_unless(
            ClassworkItem::where('id', $assignment->classwork_item_id)
                ->where('classroom_id', $classroom->id)
                ->exists(),
            404
        );
        if ($submission->assignment_id !== $assignment->id) {
            abort(404);
        }

        $this->classroom = $classroom;
        $this->assignment = $assignment;
        $this->submission = $submission;
        $this->score = $submission->score ?? 0;
        $this->feedback = $submission->feedback ?? '';
    }

    public function grade(): void
    {
        $this->validate([
            'score' => "required|integer|min:0|max:{$this->assignment->max_score}",
            'feedback' => 'nullable|string',
        ], [
            'score.required' => __('messages.grade.score_required'),
            'score.integer' => __('messages.grade.score_number'),
            'score.min' => __('messages.grade.score_min'),
            'score.max' => __('messages.grade.score_max'),
        ]);

        $this->submission->grade($this->score, $this->feedback);
        $this->submission->refresh();
        app(GamificationService::class)->awardForSubmissionGraded($this->submission);

        session()->flash('message', __('messages.assignment.graded'));
    }

    public function returnSubmission(): void
    {
        $this->validate([
            'feedback' => 'nullable|string',
        ]);

        $this->submission->returnSubmission($this->feedback);
        $this->submission->refresh();

        session()->flash('message', __('messages.assignment.returned'));
    }

    public function render()
    {
        return view('livewire.assignment.grade');
    }
}
