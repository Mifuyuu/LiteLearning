<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\ThemeCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Settings extends Component
{
    public function placeholder(array $params = [])
    {
        $title = isset($params['classroom'])
            ? 'ตั้งค่า' . ' - ' . $params['classroom']->name
            : 'ตั้งค่า';
        return view('livewire.placeholders.generic', array_merge($params, ['pageTitle' => $title]));
    }
    #[Locked]
    public Classroom $classroom;

    public string $name = '';

    public string $section = '';

    public string $description = '';

    public ?int $theme_category_id = null;

    public string $deleteConfirm = '';

    public function mount(Classroom $classroom): void
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($classroom->isOwnedBy($user) || $user->isAdmin(), 403);

        $this->classroom = $classroom;
        $this->name = $classroom->name;
        $this->section = $classroom->section ?? '';
        $this->description = $classroom->description ?? '';
        $this->theme_category_id = $classroom->theme_category_id;
    }

    public function saveSettings(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'theme_category_id' => 'nullable|integer|exists:theme_categories,id',
        ], [
            'name.required' => __('messages.validation.name_classroom'),
            'section.required' => __('messages.validation.section'),
            'description.required' => __('messages.validation.description'),
        ]);

        $this->classroom->update([
            'name' => $this->name,
            'section' => $this->section,
            'description' => $this->description,
            'theme_category_id' => $this->theme_category_id,
        ]);

        $this->classroom->refresh();

        $this->dispatch('classroom-updated', [
            'id' => $this->classroom->id,
            'name' => $this->name,
            'color' => $this->classroom->themeCategory?->color ?? '#8B5CF6',
        ]);
        $this->dispatch('notify', message: __('messages.classroom.settings_saved'));
    }

    public function toggleArchive(): void
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($this->classroom->isOwnedBy($user), 403);

        $this->classroom->is_archived = ! $this->classroom->is_archived;
        $this->classroom->save();

        $this->dispatch('notify', message: $this->classroom->is_archived ? __('messages.classroom.archived') : __('messages.classroom.restored'));
    }

    public function deleteClassroom()
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($this->classroom->isOwnedBy($user), 403);

        if (trim($this->deleteConfirm) !== $this->classroom->name) {
            $this->addError('deleteConfirm', __('messages.classroom.delete_confirm'));

            return;
        }

        $classroomName = $this->classroom->name;
        $this->classroom->delete();

        session()->flash('message', __('messages.classroom.deleted', ['name' => $classroomName]));

        return redirect()->route('classrooms');
    }

    public function render()
    {
        return view('livewire.classroom.settings', [
            'themes' => ThemeCategory::active()->orderBy('planet_number')->get(),
        ])->title($this->classroom->name.' - '.'ตั้งค่า');
    }
}
