<?php

namespace App\Livewire\Material;

use App\Livewire\Concerns\HasFileUpload;
use App\Livewire\Concerns\HasTopicSelector;
use App\Models\Classroom;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mews\Purifier\Facades\Purifier;

#[Layout('layouts.app')]
class Create extends Component
{
    use HasFileUpload, HasTopicSelector, WithFileUploads;

    #[Locked]
    public Classroom $classroom;

    public string $title = '';

    public string $description = '';

    public string $topic = '';

    public ?string $published_at = null;

    public function mount(Classroom $classroom): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($classroom->canManageClassroom($user), 403);

        $this->classroom = $classroom;
    }

    protected function allowedMimes(): string
    {
        return 'pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,txt,mp4,mp3';
    }

    protected function maxFileSizeKb(): int
    {
        return 25600;
    }

    public function save(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($this->classroom->canManageClassroom($user), 403);

        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'topic' => 'nullable|string|max:255',
            'published_at' => ['nullable', 'date', 'after:now', 'before:'.now()->addYears(5)->toDateTimeString()],
        ], [
            'title.required' => __('messages.validation.title_material'),
            'description.required' => __('messages.validation.description'),
            'topic.required' => __('messages.validation.topic'),
        ]);

        // Handle topic
        $topicName = trim($this->topic);
        $topicId = null;
        if ($topicName) {
            $topicId = $this->resolveOrCreateTopic($topicName, $this->classroom->id);
        }

        DB::transaction(function () use ($user, $topicId): void {
            $classworkItem = \App\Models\ClassworkItem::create([
                'type' => 'material',
                'classroom_id' => $this->classroom->id,
                'user_id' => $user->id,
                'topic_id' => $topicId,
                'title' => $this->title,
                'slug' => \App\Models\ClassworkItem::generateUniqueSlug(),
                'description' => $this->description ? Purifier::clean($this->description) : null,
                'published_at' => $this->published_at ?: null,
            ]);

            $material = Material::create([
                'classwork_item_id' => $classworkItem->id,
            ]);

            // Upload files to S3 and persist to polymorphic attachments table
            foreach ($this->uploadedFiles as $uploaded) {
                $path = $uploaded['file']->store(
                    'materials/attachments/'.$this->classroom->id,
                    's3'
                );
                $material->attachments()->create([
                    'file_name' => $uploaded['name'],
                    'file_path' => $path,
                    'file_type' => $uploaded['mime'],
                    'file_size' => $uploaded['size'],
                    'uploaded_by' => $user->id,
                ]);
            }

            session()->flash('message', __('messages.material.created'));

            $this->js('window.location.replace('.json_encode(route('material.show', ['classroom' => $this->classroom, 'material' => $material])).')');
        });

    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.material.create');
    }
}
