<?php

namespace App\Livewire\Assignment;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Grade extends Component
{
    public Classroom $classroom;
    public Assignment $assignment;
    public Submission $submission;
    public int $score = 0;
    public string $feedback = '';

    public function mount(Classroom $classroom, Assignment $assignment, Submission $submission)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$classroom->isOwnedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        // Ensure assignment belongs to classroom and submission belongs to assignment (prevent IDOR)
        if ($assignment->classroom_id !== $classroom->id) {
            abort(404);
        }
        if ($submission->assignment_id !== $assignment->id) {
            abort(404);
        }

        $this->classroom = $classroom;
        $this->assignment = $assignment;
        $this->submission = $submission;
        $this->score = $submission->score ?? 0;
        $this->feedback = $submission->feedback ?? '';
    }

    public function grade()
    {
        $this->validate([
            'score' => "required|integer|min:0|max:{$this->assignment->max_score}",
            'feedback' => 'nullable|string',
        ]);

        $this->submission->grade($this->score, $this->feedback);
        $this->submission->refresh();

        session()->flash('message', 'Submission graded successfully!');
    }

    public function returnSubmission()
    {
        $this->submission->returnSubmission();
        $this->submission->refresh();

        session()->flash('message', 'Submission returned to student.');
    }

    public function render()
    {
        return view('livewire.assignment.grade');
    }
}
