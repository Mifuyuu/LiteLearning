<?php

namespace App\Livewire\Assignment;

use App\Livewire\Concerns\HasFileUpload;
use App\Livewire\Concerns\HasTopicSelector;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Classroom;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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

    public ?string $published_at = null;

    public string $status = 'published';

    public string $topic = '';

    public bool $allow_late_submission = true;

    public string $type = 'file';

    public function mount(Classroom $classroom): void
    {
        $this->classroom = $classroom;
        abort_unless(
            $classroom->canManageClassroom(auth()->user()),
            403
        );

        // Allow pre-selecting a type via query string (e.g. ?type=announcement)
        $requestType = request()->query('type');
        $allowed = ['announcement', 'attendance', 'file', 'material'];
        if ($requestType && in_array($requestType, $allowed, true)) {
            $this->type = $requestType;
        }

        if ($this->type === 'attendance') {
            $this->title = 'เช็คชื่อประจำวันที่ ' . now()->format('d/m/y');
            $this->topic = 'เช็คชื่อ';
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

    private function persistAttachments(Model $attachable, int $uploadedBy): void
    {
        foreach ($this->uploadedFiles as $uploaded) {
            if (! isset($uploaded['file']) || ! $uploaded['file']) {
                continue;
            }

            $path = $uploaded['file']->store('classwork/attachments/'.$this->classroom->id, 's3');
            $attachable->attachments()->create([
                'file_name' => $uploaded['name'],
                'file_path' => $path,
                'file_type' => $uploaded['mime'],
                'file_size' => $uploaded['size'],
                'uploaded_by' => $uploadedBy,
            ]);
        }
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'max_score' => 'required_unless:type,material,topic,announcement|integer|min:0|max:100',
            'exp_reward' => 'integer|min:0|max:9999',
            'coin_reward' => 'integer|min:0|max:9999',
            'type' => 'required|in:announcement,attendance,file,material',
            'due_date' => 'nullable|date',
            'published_at' => ['nullable', 'date', 'after:now', 'before:'.now()->addYears(5)->toDateTimeString()],
            'status' => 'required|in:draft,published,scheduled',
            'topic' => 'nullable|string|max:255',
            'allow_late_submission' => 'boolean',
        ], [
            'title.required' => __('messages.validation.title_assignment'),
            'description.required' => __('messages.validation.description'),
            'max_score.required' => __('messages.validation.max_score'),
            'type.required' => __('messages.validation.type_assignment'),
            'status.required' => __('messages.validation.status'),
            'topic.required' => __('messages.validation.topic'),
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        abort_unless($this->classroom->canManageClassroom($user), 403);

        if (in_array($this->type, ['announcement', 'material', 'topic'])) {
            $this->max_score = 0;
            $this->due_date = null;
        }

        if ($this->type === 'attendance') {
            $this->title = 'เช็คชื่อประจำวันที่ ' . now()->format('d/m/y');
            $this->topic = 'เช็คชื่อ';
            $this->description = '';
            $this->due_date = null;
        }

        // Handle topic
        $topicName = trim($this->topic);
        $topicId = null;
        if ($topicName) {
            $topicId = $this->resolveOrCreateTopic($topicName, $this->classroom->id);
        }

        if ($this->published_at && now()->lt(Carbon::parse($this->published_at))) {
            $this->status = 'scheduled';
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
                    'slug' => \App\Models\ClassworkItem::generateUniqueSlug(),
                    'description' => $this->description ? Purifier::clean($this->description) : null,
                    'published_at' => $this->published_at ?: null,
                ]);

                $announcement = Announcement::create([
                    'classwork_item_id' => $classworkItem->id,
                    'content' => $this->description ? Purifier::clean($this->description) : null,
                ]);

                $this->persistAttachments($announcement, $user->id);
            });

            $this->js('window.location.replace('.json_encode(route('classroom.show', $this->classroom)).')');

            return;
        }

        DB::transaction(function () use ($user, $topicId): void {
            $classworkItem = \App\Models\ClassworkItem::create([
                'type' => 'assignment',
                'classroom_id' => $this->classroom->id,
                'user_id' => $user->id,
                'topic_id' => $topicId,
                'title' => $this->title,
                'slug' => \App\Models\ClassworkItem::generateUniqueSlug(),
                'description' => $this->description ? Purifier::clean($this->description) : null,
                'published_at' => $this->published_at ?: null,
            ]);

            $assignment = Assignment::create([
                'classwork_item_id' => $classworkItem->id,
                'max_score' => $this->max_score,
                'exp_reward' => $this->exp_reward,
                'coin_reward' => $this->coin_reward,
                'due_date' => $this->due_date,
                'status' => $this->status,
                'type' => $this->type,
                'allow_late_submission' => $this->allow_late_submission,
            ]);

            $this->persistAttachments($assignment, $user->id);

            // Create submissions for all enrolled students
            if ($this->status === 'published' && ! in_array($this->type, ['material', 'topic'])) {
                foreach ($this->classroom->students as $student) {
                    $assignment->submissions()->create([
                        'user_id' => $student->id,
                        'status' => 'assigned',
                    ]);
                }
            }

            $this->js('window.location.replace('.json_encode(route('assignment.show', [
                'classroom' => $this->classroom,
                'assignment' => $assignment,
            ])).')');
        });

    }

    public function saveDraft(): void
    {
        $this->status = 'draft';
        $this->published_at = null;

        $this->save();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.assignment.create');
    }
}
