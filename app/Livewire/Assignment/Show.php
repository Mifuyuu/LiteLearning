<?php

namespace App\Livewire\Assignment;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Submission;
use App\Models\Topic;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public Classroom $classroom;
    public Assignment $assignment;

    // Student submission
    public ?Submission $userSubmission = null;
    public string $submissionContent = '';

    // File upload
    public $uploadedFiles = [];

    // Edit mode
    public bool $isEditTab = false;
    public string $editTitle = '';
    public string $editDescription = '';
    public int $editMaxScore = 100;
    public int $editExpReward = 0;
    public int $editCoinReward = 0;
    public ?string $editDueDate = null;
    public string $editStatus = 'published';
    public string $editType = 'question';
    public string $editTopic = '';
    public bool $editAllowLateSubmission = true;

    // Delete modal
    public bool $showDeleteModal = false;

    // Teacher: submissions list
    public $submissions = null;

    public function mount(Classroom $classroom, Assignment $assignment): void
    {
        $this->classroom = $classroom;
        $this->assignment = $assignment;

        // Verify assignment belongs to this classroom via ClassroomContent
        $belongsToClassroom = \App\Models\ClassroomContent::where('contentable_type', Assignment::class)
            ->where('contentable_id', $assignment->id)
            ->where('classroom_id', $classroom->id)
            ->exists();
        abort_unless($belongsToClassroom, 404);
        // Verify access
        abort_unless($classroom->hasAccess(auth()->user()), 403);

        $user = auth()->user();

        if ($user->isStudent()) {
            $this->userSubmission = $assignment->submissionFor($user);
            $this->submissionContent = $this->userSubmission?->content ?? '';
        }

        if ($classroom->canManageClassroom($user)) {
            $this->submissions = $assignment->submissions()->with('user')->get();

            // Auto-open edit tab if ?edit=1
            if (request()->query('edit') == '1') {
                $this->openEditTab();
            }
        }
    }

    // ──────────────────────────────────────────────
    // Student: File Upload
    // ──────────────────────────────────────────────

    public function updatedUploadedFiles(): void
    {
        $this->validate([
            'uploadedFiles.*' => 'file|max:25600|mimes:' . Assignment::allowedSubmissionMimes(),
        ]);

        foreach ($this->uploadedFiles as $file) {
            $path = $file->store('submissions/' . $this->assignment->id, 's3');

            // Ensure we have a submission record
            if (!$this->userSubmission) {
                $this->userSubmission = $this->assignment->submissions()->create([
                    'user_id' => auth()->id(),
                    'status' => 'assigned',
                ]);
            }

            $this->userSubmission->attachments()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        $this->uploadedFiles = [];
        $this->userSubmission->refresh();

        session()->flash('message', __('File uploaded successfully'));
    }

    public function removeFile(int $attachmentId): void
    {
        $attachment = $this->userSubmission?->attachments()->findOrFail($attachmentId);

        abort_unless($attachment->uploaded_by === auth()->id(), 403);

        // Delete from S3
        Storage::disk('s3')->delete($attachment->file_path);
        $attachment->delete();

        $this->userSubmission->refresh();
    }

    // ──────────────────────────────────────────────
    // Student: Submit / Draft / Unsubmit
    // ──────────────────────────────────────────────

    public function turnIn(): void
    {
        $user = auth()->user();

        // Check if submission is allowed
        if (!$this->assignment->canAcceptSubmission()) {
            session()->flash('error', __('Submissions closed'));
            return;
        }

        if (!$this->userSubmission) {
            $this->userSubmission = $this->assignment->submissions()->create([
                'user_id' => $user->id,
                'status' => 'assigned',
            ]);
        }

        $wasAlreadyTurnedIn = $this->userSubmission->isTurnedIn();

        $this->userSubmission->update([
            'content' => $this->submissionContent,
        ]);

        $this->userSubmission->turnIn();

        if (!$wasAlreadyTurnedIn) {
            app(GamificationService::class)->awardForAssignmentTurnedIn($user, $this->assignment->id);
        }
    }

    public function saveDraft(): void
    {
        if (!$this->userSubmission) {
            $this->userSubmission = $this->assignment->submissions()->create([
                'user_id' => auth()->id(),
                'status' => 'assigned',
            ]);
        }

        $this->userSubmission->update([
            'content' => $this->submissionContent,
        ]);

        session()->flash('message', __('Draft saved'));
    }

    public function unsubmit(): void
    {
        $this->userSubmission?->unsubmit();
        $this->submissionContent = $this->userSubmission?->content ?? '';
    }

    // ──────────────────────────────────────────────
    // Teacher: Edit assignment
    // ──────────────────────────────────────────────

    public function openEditTab(): void
    {
        $this->isEditTab = true;
        $this->syncEditFields();
    }

    public function cancelEditTab(): void
    {
        $this->isEditTab = false;
    }

    private function syncEditFields(): void
    {
        $this->editTitle = $this->assignment->title;
        $this->editDescription = $this->assignment->description ?? '';
        $this->editMaxScore = $this->assignment->max_score;
        $this->editExpReward = $this->assignment->exp_reward;
        $this->editCoinReward = $this->assignment->coin_reward;
        $this->editDueDate = $this->assignment->due_date?->format('Y-m-d\TH:i');
        $this->editStatus = $this->assignment->status;
        $this->editType = $this->assignment->type;
        $this->editTopic = $this->assignment->topic ?? '';
        $this->editAllowLateSubmission = $this->assignment->allow_late_submission;
    }

    public function saveAssignment(): void
    {
        abort_unless($this->classroom->canManageClassroom(auth()->user()), 403);

        $this->validate([
            'editTitle' => 'required|string|max:255',
            'editDescription' => 'nullable|string',
            'editMaxScore'  => 'required_unless:editType,material|integer|min:0|max:1000',
            'editExpReward'  => 'integer|min:0|max:9999',
            'editCoinReward' => 'integer|min:0|max:9999',
            'editDueDate' => 'nullable|date',
            'editStatus' => 'required|in:draft,published,closed',
            'editType' => 'required|in:attendance,file,question,material',
            'editTopic' => 'nullable|string|max:255',
            'editAllowLateSubmission' => 'boolean',
        ]);

        $topicName = trim($this->editTopic);
        if ($topicName) {
            Topic::firstOrCreate([
                'classroom_id' => $this->classroom->id,
                'name' => $topicName,
            ]);
        }

        $this->assignment->update([
            'title' => $this->editTitle,
            'description' => $this->editDescription,
            'max_score'            => $this->editMaxScore,
            'exp_reward'           => $this->editExpReward,
            'coin_reward'          => $this->editCoinReward,
            'due_date' => $this->editDueDate,
            'status' => $this->editStatus,
            'type' => $this->editType,
            'topic' => $topicName ?: null,
            'allow_late_submission' => $this->editAllowLateSubmission,
        ]);

        $this->isEditTab = false;
        $this->assignment->refresh();

        session()->flash('message', __('Assignment updated'));
    }

    // ──────────────────────────────────────────────
    // Teacher: Delete assignment
    // ──────────────────────────────────────────────

    public function openDeleteModal(): void
    {
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
    }

    public function deleteAssignment(): void
    {
        abort_unless($this->classroom->canManageClassroom(auth()->user()), 403);

        $this->assignment->delete();

        $this->redirect(route('classroom.show', $this->classroom->slug), navigate: true);
    }

    // ──────────────────────────────────────────────
    // Computed
    // ──────────────────────────────────────────────

    public function getTopicsProperty()
    {
        return Topic::where('classroom_id', $this->classroom->id)->get();
    }

    public function render()
    {
        return view('livewire.assignment.show');
    }
}
