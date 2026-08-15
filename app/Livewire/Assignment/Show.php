<?php

namespace App\Livewire\Assignment;

use App\Livewire\Concerns\HasEditableContent;
use App\Livewire\Concerns\HasTopicSelector;
use App\Livewire\Concerns\VerifiesContentAccess;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Submission;
use App\Services\GamificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mews\Purifier\Facades\Purifier;

#[Lazy]
class Show extends Component
{
    use HasEditableContent, HasTopicSelector, VerifiesContentAccess, WithFileUploads;

    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.assignment-show', $params);
    }

    #[Locked]
    public Classroom $classroom;

    #[Locked]
    public Assignment $assignment;

    // Student submission
    public ?Submission $userSubmission = null;

    public string $submissionContent = '';

    // File upload (single file — S3 does not support multiple simultaneous uploads)
    public $uploadedFile = null;

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

    // Teacher: submissions list
    public $submissions = null;

    private function ensureStudentUser(): \App\Models\User
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($user->isStudent(), 403);

        return $user;
    }

    private function ensureSubmissionOpen(): bool
    {
        if ($this->assignment->canAcceptSubmission()) {
            return true;
        }

        session()->flash('error', __('messages.assignment.closed'));

        return false;
    }

    public function mount(Classroom $classroom, Assignment $assignment): void
    {
        $this->classroom = $classroom;
        $this->assignment = $assignment;

        // Verify assignment belongs to this classroom
        $this->verifyContentAccess($classroom, $assignment);

        $user = auth()->user();
        if (
            ! $classroom->canManageClassroom($user)
            && (
                in_array($assignment->status, ['draft', 'scheduled'], true)
                || $assignment->classworkItem?->published_at?->isFuture()
            )
        ) {
            abort(404);
        }

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

    public function updatedUploadedFile(): void
    {
        $user = $this->ensureStudentUser();

        if (! $this->ensureSubmissionOpen()) {
            $this->uploadedFile = null;

            return;
        }

        $this->validate([
            'uploadedFile' => 'file|max:25600|mimes:'.Assignment::allowedSubmissionMimes(),
        ]);

        $file = $this->uploadedFile;
        $path = $file->store('submissions/'.$this->assignment->id, 's3');

        // Ensure we have a submission record
        if (! $this->userSubmission) {
            $this->userSubmission = $this->assignment->submissions()->firstOrCreate([
                'user_id' => $user->id,
            ], [
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

        $this->uploadedFile = null;
        $this->userSubmission->refresh();

        session()->flash('message', __('messages.assignment.file_uploaded'));
    }

    public function removeFile(int $attachmentId): void
    {
        $this->ensureStudentUser();

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
        $user = $this->ensureStudentUser();

        // Check if submission is allowed
        if (! $this->ensureSubmissionOpen()) {
            return;
        }

        if (! $this->userSubmission) {
            $this->userSubmission = $this->assignment->submissions()->firstOrCreate([
                'user_id' => $user->id,
            ], [
                'status' => 'assigned',
            ]);
        }

        $wasAlreadyTurnedIn = $this->userSubmission->isTurnedIn();

        $this->userSubmission->update([
            'content' => $this->submissionContent,
        ]);

        $this->userSubmission->turnIn();

        if (! $wasAlreadyTurnedIn) {
            app(GamificationService::class)->awardForAssignmentTurnedIn($user, $this->assignment->id);
        }
    }

    public function saveDraft(): void
    {
        $user = $this->ensureStudentUser();

        if (! $this->ensureSubmissionOpen()) {
            return;
        }

        if (! $this->userSubmission) {
            $this->userSubmission = $this->assignment->submissions()->firstOrCreate([
                'user_id' => $user->id,
            ], [
                'status' => 'assigned',
            ]);
        }

        $this->userSubmission->update([
            'content' => $this->submissionContent,
        ]);

        session()->flash('message', __('messages.assignment.draft_saved'));
    }

    public function unsubmit(): void
    {
        $this->ensureStudentUser();

        if (! $this->ensureSubmissionOpen()) {
            return;
        }

        if ($this->userSubmission?->status !== 'turned_in') {
            return;
        }

        $this->userSubmission?->unsubmit();
        $this->submissionContent = $this->userSubmission?->content ?? '';
    }

    // ──────────────────────────────────────────────
    // HasEditableContent implementation
    // ──────────────────────────────────────────────

    protected function editableFields(): array
    {
        return [
            'editTitle' => 'title',
            'editDescription' => 'description',
            'editMaxScore' => 'max_score',
            'editExpReward' => 'exp_reward',
            'editCoinReward' => 'coin_reward',
            'editDueDate' => 'due_date',
            'editStatus' => 'status',
            'editType' => 'type',
            'editTopic' => 'topic',
            'editAllowLateSubmission' => 'allow_late_submission',
        ];
    }

    protected function getEditableModel(): Model
    {
        return $this->assignment;
    }

    public function saveAssignment(): void
    {
        abort_unless($this->classroom->canManageClassroom(auth()->user()), 403);

        $this->validate([
            'editTitle' => 'required|string|max:50',
            'editDescription' => 'nullable|string',
            'editMaxScore' => 'required_unless:editType,material|integer|min:0|max:100',
            'editExpReward' => 'integer|min:0|max:9999',
            'editCoinReward' => 'integer|min:0|max:9999',
            'editDueDate' => 'nullable|date',
            'editStatus' => 'required|in:draft,published,closed',
            'editType' => 'required|in:attendance,file,question,project,announcement,material,topic',
            'editTopic' => 'nullable|string|max:255',
            'editAllowLateSubmission' => 'boolean',
        ], [
            'editTitle.required' => __('messages.validation.title_assignment'),
            'editDescription.required' => __('messages.validation.description'),
            'editMaxScore.required' => __('messages.validation.max_score'),
            'editStatus.required' => __('messages.validation.status'),
            'editType.required' => __('messages.validation.type_assignment'),
            'editTopic.required' => __('messages.validation.topic'),
        ]);

        $topicName = trim($this->editTopic);
        $topicId = null;
        if ($topicName) {
            $topicId = $this->resolveOrCreateTopic($topicName, $this->classroom->id);
        }

        $this->assignment->classworkItem->update([
            'title' => $this->editTitle,
            'description' => $this->editDescription ? Purifier::clean($this->editDescription) : null,
            'topic_id' => $topicId,
        ]);

        $this->assignment->update([
            'max_score' => $this->editMaxScore,
            'exp_reward' => $this->editExpReward,
            'coin_reward' => $this->editCoinReward,
            'due_date' => $this->editDueDate,
            'status' => $this->editStatus,
            'type' => $this->editType,
            'allow_late_submission' => $this->editAllowLateSubmission,
        ]);

        $this->isEditTab = false;
        $this->assignment->refresh();

        session()->flash('message', __('messages.assignment.updated'));
    }

    public function deleteAssignment(): void
    {
        abort_unless($this->classroom->canManageClassroom(auth()->user()), 403);

        DB::transaction(function (): void {
            foreach ($this->assignment->attachments as $attachment) {
                Storage::disk('s3')->delete($attachment->file_path);
                $attachment->delete();
            }

            $this->assignment->comments()->delete();

            foreach ($this->assignment->submissions()->with('attachments')->get() as $submission) {
                foreach ($submission->attachments as $attachment) {
                    Storage::disk('s3')->delete($attachment->file_path);
                    $attachment->delete();
                }
            }

            $this->assignment->classworkItem?->delete();
        });

        $this->redirect(route('classroom.show', $this->classroom->slug), navigate: true);
    }

    public function render()
    {
        return view('livewire.assignment.show');
    }
}
