<?php

namespace App\Livewire\Material;

use App\Models\Classroom;
use App\Models\Material;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Create extends Component
{
    use WithFileUploads;

    public Classroom $classroom;

    public string $title = '';

    public string $description = '';

    public string $topic = '';

    public array $uploadedFiles = [];

    public $file;

    public function mount(Classroom $classroom): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($classroom->canManageClassroom($user), 403);

        $this->classroom = $classroom;
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

        $this->uploadedFiles[] = [
            'tmpPath' => $this->file->getRealPath(),
            'name' => $this->file->getClientOriginalName(),
            'size' => $this->file->getSize(),
            'mime' => $this->file->getMimeType(),
            'file' => $this->file,
            'id' => $this->generateAttachmentId(),
        ];

        $this->file = null;
    }

    public function removeFile(int $index): void
    {
        unset($this->uploadedFiles[$index]);
        $this->uploadedFiles = array_values($this->uploadedFiles);
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
        if ($topicName) {
            Topic::firstOrCreate([
                'classroom_id' => $this->classroom->id,
                'name' => $topicName,
            ]);
        }

        // Create material
        $material = Material::create([
            'user_id' => $user->id,
            'classroom_id' => $this->classroom->id,
            'title' => $this->title,
            'description' => $this->description ? Purifier::clean($this->description) : null,
        ]);

        // Upload files to S3 and persist to polymorphic attachments table
        foreach ($this->uploadedFiles as $uploaded) {
            $path = $uploaded['file']->store(
                'materials/attachments/'.$this->classroom->id,
                's3'
            );
            $material->attachments()->create([
                'file_name'   => $uploaded['name'],
                'file_path'   => $path,
                'file_type'   => $uploaded['mime'],
                'file_size'   => $uploaded['size'],
                'uploaded_by' => $user->id,
            ]);
        }

        session()->flash('message', __('Material created successfully.'));

        $this->redirect(
            route('material.show', ['classroom' => $this->classroom, 'material' => $material]),
            navigate: true
        );

    }
    public function render()
    {
        return view('livewire.material.create');
    }
}
