<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class LeaveClassroom extends Component
{
    public ?int $classroomId = null;

    public bool $showModal = false;

    #[On('open-leave-classroom')]
    public function openModal(int $classroomId): void
    {
        $this->classroomId = $classroomId;
        $this->showModal = true;
    }

    public function leave()
    {
        /** @var User $user */
        $user = Auth::user();

        $classroom = Classroom::findOrFail($this->classroomId);

        abort_unless($user->isStudent() && $classroom->hasMember($user) && ! $classroom->isOwnedBy($user), 403);

        $classroom->members()->detach($user->id);

        $this->showModal = false;
        $this->dispatch('notify', message: __('messages.classroom.left'));

        return redirect()->route('classrooms');
    }

    public function render()
    {
        return view('livewire.classroom.leave-classroom');
    }
}
