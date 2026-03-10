<?php

namespace App\Livewire\Material;

use App\Livewire\Concerns\HasFileUpload;
use App\Livewire\Concerns\HasTopicSelector;
use App\Models\Classroom;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
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
        ]);

        // Handle topic creation
        $topicName = trim($this->topic);
        $topicId = $this->resolveOrCreateTopic($topicName, $this->classroom->id);

        // Create material
        $material = Material::create([
            'user_id' => $user->id,
            'classroom_id' => $this->classroom->id,
            'title' => $this->title,
            'description' => $this->description ? Purifier::clean($this->description) : null,
            'topic_id' => $topicId,
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

        session()->flash('message', __('Material created successfully.'));

        $this->redirect(
            route('material.show', ['classroom' => $this->classroom, 'material' => $material]),
            navigate: true
        );

    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.material.create');
    }
}
