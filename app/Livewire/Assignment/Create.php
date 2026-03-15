<?php

namespace App\Livewire\Assignment;

use App\Livewire\Concerns\HasFileUpload;
use App\Livewire\Concerns\HasTopicSelector;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Services\GamificationService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mews\Purifier\Facades\Purifier;

class Create extends Component
{
    use HasFileUpload, HasTopicSelector, WithFileUploads;

    #[Locked]
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

    protected function allowedMimes(): string
    {
        return Assignment::allowedSubmissionMimes();
    }

    protected function maxFileSizeKb(): int
    {
        return 25600;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'max_score' => 'required_unless:type,material,topic,announcement|integer|min:0|max:1000',
            'exp_reward' => 'integer|min:0|max:9999',
            'coin_reward' => 'integer|min:0|max:9999',
            'type' => 'required|in:announcement,attendance,file,question,material,topic,project',
            'due_date' => 'nullable|date',
            'status' => 'required|in:draft,published',
            'topic' => 'nullable|string|max:255',
            'allow_late_submission' => 'boolean',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($this->classroom->canManageClassroom($user), 403);

        if (in_array($this->type, ['announcement', 'material', 'topic'])) {
            $this->max_score = 0;
            $this->due_date = null;
        }

        if ($this->type === 'attendance') {
            $this->description = '';
        }

        // Handle topic
        $topicName = trim($this->topic);
        $topicId = null;
        if ($topicName) {
            $topicId = $this->resolveOrCreateTopic($topicName, $this->classroom->id);
        }

        // Upload attachments to S3
        $attachments = [];
        foreach ($this->uploadedFiles as $uploaded) {
            if (isset($uploaded['file']) && $uploaded['file']) {
                $path = $uploaded['file']->store('assignments/attachments/'.$this->classroom->id, 's3');
                $attachments[] = [
                    'id' => $uploaded['id'],
                    'name' => $uploaded['name'],
                    'path' => $path,
                    'size' => $uploaded['size'],
                    'mime' => $uploaded['mime'],
                ];
            }
        }

        // Announcement → save to announcements table, not assignments
        if ($this->type === 'announcement') {
            DB::transaction(function () use ($user, $topicId): void {
                $classworkItem = \App\Models\ClassworkItem::create([
                    'type' => 'announcement',
                    'classroom_id' => $this->classroom->id,
                    'user_id' => $user->id,
                    'topic_id' => $topicId,
                    'title' => $this->title,
                    'slug' => \App\Models\Traits\HasSlug::generateUniqueSlug($this->title),
                    'description' => $this->description ? Purifier::clean($this->description) : null,
                ]);

                Announcement::create([
                    'classwork_item_id' => $classworkItem->id,
                    'content' => $this->description ? Purifier::clean($this->description) : null,
                ]);
            });

            $this->redirect(route('classroom.show', $this->classroom), navigate: true);

            return;
        }

        DB::transaction(function () use ($user, $topicId, $attachments): void {
            $classworkItem = \App\Models\ClassworkItem::create([
                'type' => 'assignment',
                'classroom_id' => $this->classroom->id,
                'user_id' => $user->id,
                'topic_id' => $topicId,
                'title' => $this->title,
                'slug' => \App\Models\Traits\HasSlug::generateUniqueSlug($this->title),
                'description' => $this->description ? Purifier::clean($this->description) : null,
            ]);

            $assignment = Assignment::create([
                'classwork_item_id' => $classworkItem->id,
                'attachments' => ! empty($attachments) ? $attachments : null,
                'max_score' => $this->max_score,
                'exp_reward' => $this->exp_reward,
                'coin_reward' => $this->coin_reward,
                'due_date' => $this->due_date,
                'status' => $this->status,
                'type' => $this->type,
                'allow_late_submission' => $this->allow_late_submission,
            ]);

            // Create submissions for all enrolled students
            if ($this->status === 'published' && ! in_array($this->type, ['material', 'topic'])) {
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
        });

    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.assignment.create');
    }
}
