<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\Announcement;
use App\Models\ClassroomContent;
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
            'contents.contentable.user',
            'contents.contentable.comments.user',
            'students',
            'topics',
        ]);

        // Eager-load submissions on assignment contents
        $this->classroom->contents
            ->filter(fn ($c) => $c->contentable_type === \App\Models\Assignment::class)
            ->each(fn ($c) => $c->contentable?->load('submissions'));

        // For teacher: load all assignments; for student: only published
        if (!$user->isAdmin() && !$this->classroom->isOwnedBy($user)) {
            $filtered = $this->classroom->contents->filter(function ($c) {
                if ($c->contentable_type === \App\Models\Assignment::class) {
                    return $c->contentable?->status === 'published';
                }
                return true;
            })->values();
            $this->classroom->setRelation('contents', $filtered);
        }

        // Derive announcements and assignments collections from loaded contents
        // and set them as virtual relations so the view can use $classroom->announcements
        // and $classroom->assignments without triggering Eloquent's magic-method resolution.
        $announcements = $this->classroom->contents
            ->filter(fn ($c) => $c->contentable_type === Announcement::class)
            ->map(fn ($c) => $c->contentable)
            ->filter()
            ->values();
        $this->classroom->setRelation('announcements', $announcements);

        $assignments = $this->classroom->contents
            ->filter(fn ($c) => $c->contentable_type === \App\Models\Assignment::class)
            ->map(fn ($c) => $c->contentable)
            ->filter()
            ->values();
        $this->classroom->setRelation('assignments', $assignments);
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

    protected function canManageClassroom(): bool
    {
        /** @var User $user */
        $user = Auth::user();
        return $this->classroom->canManageClassroom($user);
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
        $content = ClassroomContent::where('contentable_type', Announcement::class)
            ->where('contentable_id', $id)
            ->where('classroom_id', $this->classroom->id)
            ->first();
        abort_unless($content !== null, 404);

        /** @var User $user */
        $user = Auth::user();
        if ($announcement->user_id !== $user->id && !$this->classroom->canManageClassroom($user)) {
            abort(403);
        }

        $content->delete();
        $announcement->delete();
        // Reload all relations after deletion
        $this->loadClassroomRelations();
    }

    public function deleteAssignment(int $id)
    {
        // Fix #12: removed unused $user variable
        if (!$this->canManageClassroom()) {
            abort(403);
        }

        $content = ClassroomContent::where('contentable_type', \App\Models\Assignment::class)
            ->where('contentable_id', $id)
            ->where('classroom_id', $this->classroom->id)
            ->first();
        abort_unless($content !== null, 404);

        $assignment = $content->contentable;
        $content->delete();
        $assignment->delete();
        // Reload all relations after deletion
        $this->loadClassroomRelations();
    }

    public function render()
    {
        // Fix #3: no relationship loading here — all done in mount() and targeted refreshes
        return view('livewire.classroom.show');
    }
}
