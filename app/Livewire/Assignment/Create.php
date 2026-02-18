<?php

namespace App\Livewire\Assignment;

use App\Models\Classroom;
use App\Models\Topic;
use App\Services\GamificationService;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Assignment;
use Illuminate\Support\Facades\Storage;

class Create extends Component
{
    use WithFileUploads;

    public Classroom $classroom;

    // Form fields
    public string $title = '';
    public string $instructions = '';
    public int $max_score = 100;
    public ?string $due_date = null;
    public string $status = 'published';
    public string $topic = '';
    public bool $allow_late_submission = true;

    // File upload - single file at a time, accumulated into uploadedFiles
    public $file = null;
    public array $uploadedFiles = [];

    public function mount(Classroom $classroom): void
    {
        $this->classroom = $classroom;
        abort_unless(
            $classroom->isOwnedBy(auth()->user()) || auth()->user()->isAdmin(),
            403
        );
    }

    public function getTopicsProperty()
    {
        return Topic::where('classroom_id', $this->classroom->id)->get();
    }

    public function updatedFile(): void
    {
        $this->validate([
            'file' => 'file|max:25600',
        ]);

        if ($this->file) {
            $this->uploadedFiles[] = [
                'tmpPath' => $this->file->getRealPath(),
                'name' => $this->file->getClientOriginalName(),
                'size' => $this->file->getSize(),
                'mime' => $this->file->getMimeType(),
                'file' => $this->file,
            ];
            $this->file = null;
        }
    }

    public function removeFile($index): void
    {
        unset($this->uploadedFiles[$index]);
        $this->uploadedFiles = array_values($this->uploadedFiles);
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'max_score' => 'required|integer|min:0|max:1000',
            'due_date' => 'nullable|date',
            'status' => 'required|in:draft,published',
            'topic' => 'nullable|string|max:255',
            'allow_late_submission' => 'boolean',
        ]);

        $user = auth()->user();

        // Handle topic
        $topicName = trim($this->topic);
        if ($topicName) {
            Topic::firstOrCreate([
                'classroom_id' => $this->classroom->id,
                'name' => $topicName,
            ]);
        }

        // Upload attachments to S3
        $attachments = [];
        foreach ($this->uploadedFiles as $uploaded) {
            if (isset($uploaded['file']) && $uploaded['file']) {
                $path = $uploaded['file']->store('assignments/attachments/' . $this->classroom->id, 's3');
                $attachments[] = [
                    'id' => $this->generateAttachmentId(),
                    'name' => $uploaded['name'],
                    'path' => $path,
                    'size' => $uploaded['size'],
                    'mime' => $uploaded['mime'],
                ];
            }
        }

        $assignment = Assignment::create([
            'classroom_id' => $this->classroom->id,
            'user_id' => $user->id,
            'title' => $this->title,
            'instructions' => $this->instructions,
            'attachments' => !empty($attachments) ? $attachments : null,
            'max_score' => $this->max_score,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'type' => 'question',
            'topic' => $topicName ?: null,
            'allow_late_submission' => $this->allow_late_submission,
        ]);

        // Create submissions for all enrolled students
        if ($this->status === 'published') {
            foreach ($this->classroom->students as $student) {
                $assignment->submissions()->create([
                    'user_id' => $student->id,
                    'status' => 'assigned',
                ]);
            }
        }

        app(GamificationService::class)->awardForAssignmentCreated($user, $assignment->id);

        $this->redirect(route('assignment.show', [
            'classroom' => $this->classroom,
            'assignment' => $assignment,
        ]), navigate: true);
    }

    public function render()
    {
        return view('livewire.assignment.create');
    }

    private function generateAttachmentId(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $id = '';
        for ($i = 0; $i < 8; $i++) {
            $id .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $id;
    }
}
