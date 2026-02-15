<?php

namespace App\Livewire\Assignment;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Submission;
use App\Models\User;
use App\Services\GamificationService;
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

    public bool $isEditTab = false;
    public string $editTitle = '';
    public string $editDescription = '';
    public string $editInstructions = '';
    public int $editMaxScore = 100;
    public ?string $editDueDate = null;
    public string $editType = 'assignment';
    public string $editTopic = '';
    public string $editStatus = 'published';
    public bool $showDeleteModal = false;

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

        $this->syncEditFields();
    }

    public function openEditTab()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->classroom->isOwnedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        $this->syncEditFields();
        $this->resetValidation();
        $this->isEditTab = true;
    }

    public function cancelEditTab()
    {
        $this->syncEditFields();
        $this->resetValidation();
        $this->isEditTab = false;
    }

    public function saveAssignment()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->classroom->isOwnedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        $this->validate([
            'editTitle' => 'required|string|max:255',
            'editDescription' => 'nullable|string',
            'editInstructions' => 'nullable|string',
            'editMaxScore' => 'required|integer|min:0|max:1000',
            'editDueDate' => 'nullable|date',
            'editType' => 'required|in:assignment,quiz,material',
            'editTopic' => 'nullable|string|max:255',
            'editStatus' => 'required|in:draft,published',
        ]);

        $this->assignment->update([
            'title' => $this->editTitle,
            'description' => $this->editDescription,
            'instructions' => $this->editInstructions,
            'max_score' => $this->editType === 'material' ? 0 : $this->editMaxScore,
            'due_date' => $this->editType === 'material' ? null : $this->editDueDate,
            'type' => $this->editType,
            'topic' => $this->editTopic,
            'status' => $this->editStatus,
        ]);

        $this->assignment->refresh();
        $this->syncEditFields();
        $this->isEditTab = false;

        session()->flash('message', __('Assignment updated successfully.'));
    }

    public function deleteAssignment()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->classroom->isOwnedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        $this->showDeleteModal = false;
        $this->assignment->delete();

        session()->flash('message', __('Assignment deleted successfully.'));

        return redirect()->route('classroom.show', $this->classroom);
    }

    public function openDeleteModal()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->classroom->isOwnedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
    }

    public function turnIn()
    {
        $wasAlreadyTurnedIn = $this->userSubmission?->status === 'turned_in';

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

        if (!$wasAlreadyTurnedIn) {
            /** @var User $user */
            $user = Auth::user();
            app(GamificationService::class)->awardForAssignmentTurnedIn($user, $this->assignment->id);
        }

        session()->flash('message', __('Assignment turned in successfully!'));
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

        session()->flash('message', __('Draft saved!'));
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

    private function syncEditFields(): void
    {
        $this->editTitle = $this->assignment->title;
        $this->editDescription = $this->assignment->description ?? '';
        $this->editInstructions = $this->assignment->instructions ?? '';
        $this->editMaxScore = (int) $this->assignment->max_score;
        $this->editDueDate = $this->assignment->due_date?->format('Y-m-d\TH:i');
        $this->editType = $this->assignment->type;
        $this->editTopic = $this->assignment->topic ?? '';
        $this->editStatus = $this->assignment->status;
    }
}
