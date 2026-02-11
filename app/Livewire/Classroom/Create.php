<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';
    public string $section = '';
    public string $subject = '';
    public string $description = '';
    public string $theme_color = '#4F46E5';
    public bool $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'section' => 'nullable|string|max:255',
        'subject' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:1000',
        'theme_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
    ];

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'section', 'subject', 'description']);
        $this->theme_color = '#4F46E5';
        $this->showModal = true;
    }

    public function create()
    {
        // Only teachers and admins can create classrooms
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isTeacher() && !$user->isAdmin()) {
            abort(403, 'Only teachers can create classrooms.');
        }

        $this->validate();

        $classroom = Classroom::create([
            'teacher_id' => Auth::id(),
            'name' => $this->name,
            'section' => $this->section,
            'subject' => $this->subject,
            'description' => $this->description,
            'theme_color' => $this->theme_color,
        ]);

        $this->showModal = false;
        $this->dispatch('classroom-created');

        return redirect()->route('classroom.show', $classroom);
    }

    public function render()
    {
        return view('livewire.classroom.create');
    }
}
