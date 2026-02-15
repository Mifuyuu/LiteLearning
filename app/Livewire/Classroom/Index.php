<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\ClassroomSidebarPreference;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public string $search = '';
    public string $filter = 'all';

    public function togglePin(int $classroomId): void
    {
        /** @var User $user */
        $user = Auth::user();

        $classroom = Classroom::findOrFail($classroomId);
        if (!$classroom->hasAccess($user)) {
            abort(403);
        }

        $preference = ClassroomSidebarPreference::firstOrCreate(
            [
                'user_id' => $user->id,
                'classroom_id' => $classroom->id,
            ],
            [
                'is_pinned' => false,
            ]
        );

        if ($preference->is_pinned) {
            $preference->update([
                'is_pinned' => false,
                'position' => null,
                'pinned_at' => null,
            ]);

            session()->flash('message', __('Unpinned from sidebar.'));
            $this->dispatch('sidebar-classroom-pinned-updated',
                classroomId: $classroom->id,
                slug: $classroom->slug,
                name: $classroom->name,
                themeColor: $classroom->theme_color,
                url: route('classroom.show', $classroom),
                pinned: false,
                enrolled: $classroom->hasMember($user),
                teaching: $classroom->isOwnedBy($user),
            );
            return;
        }

        $nextPosition = (int) ClassroomSidebarPreference::query()
            ->where('user_id', $user->id)
            ->where('is_pinned', true)
            ->max('position');

        $preference->update([
            'is_pinned' => true,
            'position' => $nextPosition + 1,
            'pinned_at' => now(),
        ]);

        session()->flash('message', __('Pinned to sidebar.'));
        $this->dispatch('sidebar-classroom-pinned-updated',
            classroomId: $classroom->id,
            slug: $classroom->slug,
            name: $classroom->name,
            themeColor: $classroom->theme_color,
            url: route('classroom.show', $classroom),
            pinned: true,
            enrolled: $classroom->hasMember($user),
            teaching: $classroom->isOwnedBy($user),
        );
    }

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

        $pinnedIds = ClassroomSidebarPreference::query()
            ->where('user_id', $user->id)
            ->where('is_pinned', true)
            ->pluck('classroom_id')
            ->all();

        return view('livewire.classroom.index', [
            'classrooms' => $classrooms,
            'pinnedIds' => $pinnedIds,
        ]);
    }
}
