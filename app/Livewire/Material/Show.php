<?php

namespace App\Livewire\Material;

use App\Livewire\Concerns\HasEditableContent;
use App\Livewire\Concerns\HasTopicSelector;
use App\Livewire\Concerns\VerifiesContentAccess;
use App\Models\Classroom;
use App\Models\Material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public string $editTitle = '';

    public string $editDescription = '';

    public string $editTopic = '';

    public function mount(Classroom $classroom, Material $material): void
    {
        $this->classroom = $classroom;
        $this->material = $material;
        $this->verifyContentAccess($classroom, $material);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless(
            $classroom->canManageClassroom($user) || ! $material->classworkItem?->published_at?->isFuture(),
            404
        );
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
        ], [
            'editTitle.required' => __('messages.validation.title_material'),
            'editDescription.required' => __('messages.validation.description'),
            'editTopic.required' => __('messages.validation.topic'),
        ]);

        $topicName = trim($this->editTopic);
        $topicId = null;
        if ($topicName) {
            $topicId = $this->resolveOrCreateTopic($topicName, $this->classroom->id);
        }

        $this->material->classworkItem->update([
            'title' => $this->editTitle,
            'description' => $this->editDescription ? Purifier::clean($this->editDescription) : null,
            'topic_id' => $topicId,
        ]);

        $this->isEditTab = false;
        $this->material->refresh();

        $this->dispatch('notify', message: __('messages.material.updated'), type: 'success');
    }

    public function deleteMaterial(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($this->classroom->canManageClassroom($user), 403);

        DB::transaction(function (): void {
            foreach ($this->material->attachments as $attachment) {
                Storage::disk('s3')->delete($attachment->file_path);
                $attachment->delete();
            }

            $this->material->comments()->delete();
            $this->material->classworkItem?->delete();
        });

        $this->redirect(route('classroom.show', $this->classroom->slug), navigate: true);
    }

    protected function editableFields(): array
    {
        return [
            'editTitle' => 'title',
            'editDescription' => 'description',
            'editTopic' => 'topic',
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
