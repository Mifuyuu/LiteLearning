<?php

namespace App\Livewire\Material;

use App\Models\Classroom;
use App\Models\Material;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Show extends Component
{
    use WithFileUploads;

    public Classroom $classroom;

    public Material $material;

    // Edit mode
    public bool $isEditTab = false;

    public string $editTitle = '';

    public string $editDescription = '';

    public string $editTopic = '';

    // Delete modal
    public bool $showDeleteModal = false;

    public function mount(Classroom $classroom, Material $material): void
    {
        // Verify material belongs to this classroom
        abort_unless($material->classroom_id === $classroom->id, 404);

        // Verify access
        abort_unless($classroom->hasAccess(auth()->user()), 403);

        $this->classroom = $classroom;
        $this->material = $material;
    }

    // ──────────────────────────────────────────────
    // Teacher: Edit material
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
        $this->editTitle = $this->material->title;
        $this->editDescription = $this->material->description ?? '';

        // Materials don't have a topic column
        $this->editTopic = '';
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
            Topic::firstOrCreate([
                'classroom_id' => $this->classroom->id,
                'name' => $topicName,
            ]);
        }

        $this->material->update([
            'title' => $this->editTitle,
            'description' => $this->editDescription ?: null,
        ]);

        $this->isEditTab = false;
        $this->material->refresh();

        session()->flash('message', __('Material updated'));
    }

    // ──────────────────────────────────────────────
    // Teacher: Delete material
    // ──────────────────────────────────────────────

    public function openDeleteModal(): void
    {
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
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

    public function getTopicsProperty()
    {
        return Topic::where('classroom_id', $this->classroom->id)->get();
    }

    public function render()
    {
        return view('livewire.material.show');
    }
}
