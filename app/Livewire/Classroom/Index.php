<?php

namespace App\Livewire\Classroom;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public string $search = '';

    public string $filter = 'all';

    public bool $showArchived = false;

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isTeacher()) {
            $classrooms = $user->ownedClassrooms()
                ->when(! $this->showArchived, fn ($q) => $q->where('is_archived', false))
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->with('teacher', 'themeCategory')
                ->get();
        } elseif ($this->filter === 'enrolled') {
            $classrooms = $user->enrolledClassrooms()
                ->where('is_archived', false)
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->with('teacher', 'themeCategory')
                ->get();
        } elseif ($this->filter === 'archived') {
            $owned = $user->ownedClassrooms()
                ->where('is_archived', true)
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->with('teacher', 'themeCategory')
                ->get();
            $enrolled = $user->enrolledClassrooms()
                ->where('is_archived', true)
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->with('teacher', 'themeCategory')
                ->get();
            $classrooms = $owned->merge($enrolled)->unique('id')->values();
        } else {
            $enrolled = $user->enrolledClassrooms()
                ->where('is_archived', false)
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->with('teacher', 'themeCategory')
                ->get();
            $classrooms = $enrolled;
        }

        return view('livewire.classroom.index', [
            'classrooms' => $classrooms,
        ]);
    }
}
