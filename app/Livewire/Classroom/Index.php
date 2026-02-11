<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public string $search = '';
    public string $filter = 'all';

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($this->filter === 'teaching') {
            $classrooms = $user->ownedClassrooms()
                ->where('is_archived', false)
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->get();
        } elseif ($this->filter === 'enrolled') {
            $classrooms = $user->enrolledClassrooms()
                ->where('is_archived', false)
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->get();
        } elseif ($this->filter === 'archived') {
            $owned = $user->ownedClassrooms()
                ->where('is_archived', true)
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->get();
            $enrolled = $user->enrolledClassrooms()
                ->where('is_archived', true)
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->get();
            $classrooms = $owned->merge($enrolled);
        } else {
            $classrooms = collect();
            if ($user->isTeacher() || $user->isAdmin()) {
                $classrooms = $user->ownedClassrooms()
                    ->where('is_archived', false)
                    ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->get();
            }
            $enrolled = $user->enrolledClassrooms()
                ->where('is_archived', false)
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->get();
            $classrooms = $classrooms->merge($enrolled);
        }

        return view('livewire.classroom.index', [
            'classrooms' => $classrooms,
        ]);
    }
}
