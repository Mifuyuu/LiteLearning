<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Classroom $classroom;
    public string $activeTab = 'stream';
    public string $newAnnouncement = '';

    public function mount(Classroom $classroom)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$classroom->hasAccess($user)) {
            abort(403);
        }

        $this->classroom = $classroom;
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function postAnnouncement()
    {
        $this->validate([
            'newAnnouncement' => 'required|string|min:1',
        ]);

        Announcement::create([
            'classroom_id' => $this->classroom->id,
            'user_id' => Auth::id(),
            'content' => $this->newAnnouncement,
        ]);

        $this->reset('newAnnouncement');
        $this->classroom->refresh();
    }

    public function deleteAnnouncement(int $id)
    {
        $announcement = Announcement::findOrFail($id);

        // Ensure announcement belongs to this classroom (prevent cross-classroom deletion)
        if ($announcement->classroom_id !== $this->classroom->id) {
            abort(404);
        }

        /** @var User $user */
        $user = Auth::user();
        if ($announcement->user_id !== $user->id && !$this->classroom->isOwnedBy($user)) {
            abort(403);
        }

        $announcement->delete();
        $this->classroom->refresh();
    }

    public function render()
    {
        $this->classroom->load([
            'teacher',
            'announcements.user',
            'announcements.comments.user',
            'assignments' => fn($q) => $q->published(),
            'assignments.submissions',
            'students',
            'topics',
        ]);

        return view('livewire.classroom.show');
    }
}
