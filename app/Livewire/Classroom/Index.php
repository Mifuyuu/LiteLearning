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

    public bool $showArchived = false;

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isTeacher()) {
            $ownedClassrooms = $user->ownedClassrooms()
                ->when(! $this->showArchived, fn ($q) => $q->where('is_archived', false))
                ->with('teacher', 'themeCategory')
                ->get();

            $coTeachingClassrooms = $user->enrolledClassrooms()
                ->wherePivot('role', 'co-teacher')
                ->when(! $this->showArchived, fn ($q) => $q->where('is_archived', false))
                ->with('teacher', 'themeCategory')
                ->get();

            $classrooms = $ownedClassrooms
                ->merge($coTeachingClassrooms)
                ->unique('id')
                ->when($this->search, fn ($collection) => $collection->filter(
                    fn ($classroom) => str_contains(strtolower($classroom->name), strtolower($this->search))
                ))
                ->values();
        } else {
            $classrooms = $user->enrolledClassrooms()
                ->where('is_archived', false)
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->with('teacher', 'themeCategory')
                ->get();
        }

        return view('livewire.classroom.index', [
            'classrooms' => $classrooms,
        ]);
    }
}
