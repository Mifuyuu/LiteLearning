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
class Show extends Component
{
    public Classroom $classroom;
    public Assignment $assignment;
    public string $submissionContent = '';
    public ?Submission $userSubmission = null;

    public function mount(Classroom $classroom, Assignment $assignment)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$classroom->hasAccess($user)) {
            abort(403);
        }

        // Ensure assignment belongs to this classroom (prevent IDOR)
        if ($assignment->classroom_id !== $classroom->id) {
            abort(404);
        }

        $this->classroom = $classroom;
        $this->assignment = $assignment;

        // Load user's submission if student
        if ($user->isStudent()) {
            $this->userSubmission = $assignment->submissionFor($user);
            if ($this->userSubmission) {
                $this->submissionContent = $this->userSubmission->content ?? '';
            }
        }
    }

    public function turnIn()
    {
        if (!$this->userSubmission) {
            $this->userSubmission = Submission::create([
                'assignment_id' => $this->assignment->id,
                'user_id' => Auth::id(),
                'content' => $this->submissionContent,
                'status' => 'turned_in',
                'turned_in_at' => now(),
            ]);
        } else {
            $this->userSubmission->update([
                'content' => $this->submissionContent,
                'status' => 'turned_in',
                'turned_in_at' => now(),
            ]);
        }

        session()->flash('message', 'Assignment turned in successfully!');
    }

    public function unsubmit()
    {
        if ($this->userSubmission) {
            $this->userSubmission->unsubmit();
            $this->userSubmission->refresh();
        }
    }

    public function saveDraft()
    {
        if (!$this->userSubmission) {
            $this->userSubmission = Submission::create([
                'assignment_id' => $this->assignment->id,
                'user_id' => Auth::id(),
                'content' => $this->submissionContent,
                'status' => 'assigned',
            ]);
        } else {
            $this->userSubmission->update([
                'content' => $this->submissionContent,
            ]);
        }

        session()->flash('message', 'Draft saved!');
    }

    public function render()
    {
        $this->assignment->load(['user', 'comments.user', 'attachments']);

        $submissions = null;
        /** @var User $user */
        $user = Auth::user();
        if ($this->classroom->isOwnedBy($user) || $user->isAdmin()) {
            $submissions = $this->assignment->submissions()->with('user')->get();
        }

        return view('livewire.assignment.show', [
            'submissions' => $submissions,
        ]);
    }
}
