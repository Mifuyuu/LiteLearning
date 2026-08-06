<?php

namespace App\Livewire\Classroom;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Index extends Component
{
    public function placeholder()
    {
        return view('livewire.placeholders.classroom-index');
    }
    public string $search = '';

    public bool $showArchived = false;

    private ?Collection $cachedClassrooms = null;

    public function updatedSearch(): void
    {
        $this->cachedClassrooms = null;
    }

    public function updatedShowArchived(): void
    {
        $this->cachedClassrooms = null;
    }

    public function getClassroomsProperty(): Collection
    {
        if ($this->cachedClassrooms !== null) {
            return $this->cachedClassrooms;
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->isTeacher()) {
            $owned = $user->ownedClassrooms()
                ->when(! $this->showArchived, fn ($q) => $q->where('is_archived', false))
                ->with('teacher', 'themeCategory')
                ->get();

            $coTeaching = $user->enrolledClassrooms()
                ->wherePivot('role', 'co-teacher')
                ->when(! $this->showArchived, fn ($q) => $q->where('is_archived', false))
                ->with('teacher', 'themeCategory')
                ->get();

            $this->cachedClassrooms = $owned->merge($coTeaching)->unique('id');
        } else {
            $this->cachedClassrooms = $user->enrolledClassrooms()
                ->where('is_archived', false)
                ->with('teacher', 'themeCategory')
                ->get();
        }

        if ($this->search) {
            $this->cachedClassrooms = $this->cachedClassrooms->filter(
                fn ($classroom) => str_contains(
                    strtolower($classroom->name),
                    strtolower($this->search)
                )
            )->values();
        }

        return $this->cachedClassrooms;
    }

    public function render()
    {
        return view('livewire.classroom.index', [
            'classrooms' => $this->classrooms,
        ]);
    }
}
