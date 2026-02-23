<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class People extends Component
{
    public Classroom $classroom;
    public string $inviteEmail = '';

    public function mount(Classroom $classroom)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$classroom->hasAccess($user)) {
            abort(403);
        }

        $this->classroom = $classroom;
    }

    public function removeMember(int $userId)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->classroom->isOwnedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        $this->classroom->members()->detach($userId);
        $this->classroom->refresh();

        session()->flash('message', __('Member removed successfully.'));
    }

    public function removeAllMembers()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->classroom->isOwnedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        $this->classroom->members()->detach();
        $this->classroom->refresh();

        session()->flash('message', __('All students removed successfully.'));
    }

    public function render()
    {
        $this->classroom->load(['teacher', 'members']);

        return view('livewire.classroom.people');
    }
}
