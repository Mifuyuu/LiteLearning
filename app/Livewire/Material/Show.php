<?php

namespace App\Livewire\Material;

use App\Livewire\Concerns\HasEditableContent;
use App\Livewire\Concerns\HasTopicSelector;
use App\Livewire\Concerns\VerifiesContentAccess;
use App\Models\Classroom;
use App\Models\Material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mews\Purifier\Facades\Purifier;

#[Layout('layouts.app')]
class Show extends Component
{
    use HasEditableContent, HasTopicSelector, VerifiesContentAccess, WithFileUploads;

    #[Locked]
    public Classroom $classroom;

    #[Locked]
    public Material $material;

    public function mount(Classroom $classroom, Material $material): void
    {
        $this->classroom = $classroom;
        $this->material = $material;
        $this->verifyContentAccess($classroom, $material);
    }

    public function saveMaterial(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($this->classroom->canManageClassroom($user), 403);

        $this->validate([
            'editTitle' => 'required|string|max:255',
            'editDescription' => 'nullable|string',
            'editTopic' => 'nullable|string|max:255',
        ]);

        $topicName = trim($this->editTopic);
        if ($topicName) {
            $this->resolveOrCreateTopic($topicName, $this->classroom->id);
        }

        $this->material->update([
            'title' => $this->editTitle,
            'description' => $this->editDescription ? Purifier::clean($this->editDescription) : null,
        ]);

        $this->isEditTab = false;
        $this->material->refresh();

        session()->flash('message', __('Material updated'));
    }

    public function deleteMaterial(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($this->classroom->canManageClassroom($user), 403);

        // Delete attachments from S3 and remove from DB
        foreach ($this->material->attachments as $attachment) {
            Storage::disk('s3')->delete($attachment->file_path);
            $attachment->delete();
        }

        $this->material->delete();

        $this->redirect(route('classroom.show', $this->classroom->slug), navigate: true);
    }

    protected function editableFields(): array
    {
        return [
            'editTitle' => 'title',
            'editDescription' => 'description',
            'editTopic' => '',
        ];
    }

    protected function getEditableModel(): Model
    {
        return $this->material;
    }

    public function render()
    {
        return view('livewire.material.show');
    }
}
