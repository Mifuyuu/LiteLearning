<?php

namespace App\Livewire\Assignment;

use App\Models\Assignment;
use App\Models\Announcement;
use App\Models\Classroom;

use App\Models\Topic;
use App\Services\GamificationService;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Classroom $classroom;

    // Form fields
    public string $title = '';
    public string $description = '';
    public int $max_score = 100;
    public int $exp_reward = 0;
    public int $coin_reward = 0;
    public ?string $due_date = null;
    public string $status = 'published';
    public string $topic = '';
    public bool $allow_late_submission = true;
    public string $type = 'question';

    // File upload - single file at a time, accumulated into uploadedFiles
    public $file = null;
    public array $uploadedFiles = [];

    public function mount(Classroom $classroom): void
    {
        $this->classroom = $classroom;
        abort_unless(
            $classroom->canManageClassroom(auth()->user()),
            403
        );

        // Allow pre-selecting a type via query string (e.g. ?type=announcement)
        $requestType = request()->query('type');
        $allowed = ['announcement', 'attendance', 'file', 'question', 'material', 'topic', 'project'];
        if ($requestType && in_array($requestType, $allowed, true)) {
            $this->type = $requestType;
        }
    }

    public function getTopicsProperty()
    {
        return Topic::where('classroom_id', $this->classroom->id)->get();
    }

    public function updatedFile(): void
    {
        $this->validate([
            'file' => 'file|max:25600|mimes:' . Assignment::allowedSubmissionMimes(),
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
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'max_score'  => 'required_unless:type,material,topic,announcement|integer|min:0|max:1000',
            'exp_reward'  => 'integer|min:0|max:9999',
            'coin_reward' => 'integer|min:0|max:9999',
            'type' => 'required|in:announcement,attendance,file,question,material,topic,project',
            'due_date' => 'nullable|date',
            'status' => 'required|in:draft,published',
            'topic' => 'nullable|string|max:255',
            'allow_late_submission' => 'boolean',
        ]);

        $user = auth()->user();

        if (in_array($this->type, ['announcement', 'material', 'topic'])) {
            $this->max_score = 0;
            $this->due_date = null;
        }


        if ($this->type === 'attendance') {
            $this->description = '';
        }


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

        // Announcement → save to announcements table, not assignments
        if ($this->type === 'announcement') {
            Announcement::create([
                'user_id'      => $user->id,
                'classroom_id' => $this->classroom->id,
                'title'        => $this->title,
                'content'      => $this->description ? Purifier::clean($this->description) : null,
            ]);

            $this->redirect(route('classroom.show', $this->classroom), navigate: true);
            return;
        }

        $assignment = Assignment::create([
            'user_id' => $user->id,
            'classroom_id' => $this->classroom->id,
            'title' => $this->title,
            'description' => $this->description ? Purifier::clean($this->description) : null,
            'attachments' => !empty($attachments) ? $attachments : null,
            'max_score'            => $this->max_score,
            'exp_reward'           => $this->exp_reward,
            'coin_reward'          => $this->coin_reward,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'type' => $this->type,
            'topic' => $topicName ?: null,
            'allow_late_submission' => $this->allow_late_submission,
        ]);


        // Create submissions for all enrolled students
        if ($this->status === 'published' && !in_array($this->type, ['material', 'topic'])) {
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
