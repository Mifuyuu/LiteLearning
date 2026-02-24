<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Classroom $classroom;
    #[Url(as: 'tab', except: 'stream')]
    public string $activeTab = 'stream';
    public string $newAnnouncement = '';
    public string $name = '';
    public string $section = '';
    public string $subject = '';
    public string $description = '';
    public string $theme_color = '#4F46E5';
    public string $deleteConfirm = '';

    public function mount(Classroom $classroom)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$classroom->hasAccess($user)) {
            abort(403);
        }

        $this->classroom = $classroom;
        $this->name        = $classroom->name;
        $this->section     = $classroom->section ?? '';
        $this->subject     = $classroom->subject ?? '';
        $this->description = $classroom->description ?? '';
        $this->theme_color = $classroom->theme_color;

        // Fix #3: eager-load all relationships once in mount() instead of on every render()
        $this->loadClassroomRelations();
    }

    /**
     * Eager-load all relationships needed by the view.
     * Called once in mount() and selectively after mutations.
     */
    private function loadClassroomRelations(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->classroom->load([
            'teacher',
            'announcements.user',
            'announcements.comments.user',
            'assignments' => function ($q) use ($user) {
                if ($user->isAdmin() || $this->classroom->isOwnedBy($user)) {
                    return $q;
                }
                return $q->published();
            },
            'assignments.submissions',
            'students',
            'topics',
        ]);
    }

    public function setTab(string $tab)
    {
        $allowedTabs = ['stream', 'classwork', 'people'];

        /** @var User $user */
        $user = Auth::user();
        if ($this->classroom->isOwnedBy($user)) {
            $allowedTabs[] = 'grades';
            $allowedTabs[] = 'settings';
        }

        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'stream';
        }

        $this->activeTab = $tab;
    }

    public function postAnnouncement()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->classroom->isOwnedBy($user)) {
            abort(403);
        }

        $this->validate([
            'newAnnouncement' => 'required|string|min:1',
        ]);

        Announcement::create([
            'classroom_id' => $this->classroom->id,
            'user_id'      => Auth::id(),
            'content'      => $this->newAnnouncement,
        ]);

        $this->reset('newAnnouncement');
        // Reload only announcements after posting
        $this->classroom->load(['announcements.user', 'announcements.comments.user']);
    }

    protected function canManageClassroom(): bool
    {
        /** @var User $user */
        $user = Auth::user();
        return $this->classroom->isOwnedBy($user);
    }

    public function saveSettings()
    {
        if (!$this->canManageClassroom()) {
            abort(403);
        }

        $this->validate([
            'name'        => 'required|string|max:255',
            'section'     => 'nullable|string|max:255',
            'subject'     => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'theme_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $this->classroom->update([
            'name'        => $this->name,
            'section'     => $this->section,
            'subject'     => $this->subject,
            'description' => $this->description,
            'theme_color' => $this->theme_color,
        ]);

        $this->classroom->refresh();

        $this->dispatch('classroom-updated', [
            'id'    => $this->classroom->id,
            'name'  => $this->name,
            'color' => $this->theme_color,
        ]);

        session()->flash('message', __('Classroom settings saved successfully.'));
    }

    public function deleteClassroom()
    {
        if (!$this->canManageClassroom()) {
            abort(403);
        }

        if (trim($this->deleteConfirm) !== $this->classroom->name) {
            $this->addError('deleteConfirm', __('Please type the classroom name exactly to confirm deletion.'));
            return;
        }

        $classroomName = $this->classroom->name;
        $this->classroom->delete();

        session()->flash('message', __('Classroom ":name" was deleted.', ['name' => $classroomName]));

        return redirect()->route('classrooms');
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
        // Reload only announcements after deletion
        $this->classroom->load(['announcements.user', 'announcements.comments.user']);
    }

    public function deleteAssignment(int $id)
    {
        // Fix #12: removed unused $user variable
        if (!$this->canManageClassroom()) {
            abort(403);
        }

        $assignment = \App\Models\Assignment::where('classroom_id', $this->classroom->id)
            ->findOrFail($id);

        $assignment->delete();
        // Reload only assignments after deletion
        $this->classroom->load(['assignments.submissions', 'topics']);
    }

    public function render()
    {
        // Fix #3: no relationship loading here — all done in mount() and targeted refreshes
        return view('livewire.classroom.show');
    }
}
