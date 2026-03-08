<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\ThemeCategory;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public string $section = '';

    public string $description = '';

    public ?int $theme_category_id = null;

    public bool $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'section' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:1000',
        'theme_category_id' => 'nullable|integer|exists:theme_categories,id',
    ];

    public function openModal(): void
    {
        $this->resetValidation();
        $this->reset(['name', 'section', 'description']);
        $this->theme_category_id = null;
    }

    public function create()
    {
        // Only teachers and admins can create classrooms
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (! $user->isTeacher() && ! $user->isAdmin()) {
            abort(403, 'Only teachers can create classrooms.');
        }

        $this->validate();

        $classroom = Classroom::create([
            'teacher_id' => Auth::id(),
            'name' => $this->name,
            'section' => $this->section,
            'description' => $this->description,
            'theme_category_id' => $this->theme_category_id,
        ]);

        app(GamificationService::class)->awardForClassroomCreated($user, $classroom->id);

        $this->showModal = false;
        $this->dispatch('classroom-created');

        return redirect()->route('classroom.show', $classroom);
    }

    public function render()
    {
        $themes = ThemeCategory::active()->orderBy('sort_order')->get();

        return view('livewire.classroom.create', compact('themes'));
    }
}
