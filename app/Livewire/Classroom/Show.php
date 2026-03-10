<?php

namespace App\Livewire\Classroom;

use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    #[Locked]
    public Classroom $classroom;

    #[Url(as: 'tab', except: 'stream')]
    public string $activeTab = 'stream';

    public string $name = '';

    public string $section = '';

    public string $description = '';

    public ?int $theme_category_id = null;

    public string $deleteConfirm = '';

    public bool $showDeleteAnnouncementModal = false;

    public ?int $deleteAnnouncementId = null;

    public function mount(Classroom $classroom)
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $classroom->hasAccess($user)) {
            abort(403);
        }

        $this->classroom = $classroom;
        $this->name = $classroom->name;
        $this->section = $classroom->section ?? '';
        $this->description = $classroom->description ?? '';
        $this->theme_category_id = $classroom->theme_category_id;

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
            'students',
            'topics',
        ]);

        // Load announcements with comments and users
        $announcementsQuery = $this->classroom->announcements()->with(['user', 'comments.user']);
        $announcements = $announcementsQuery->latest()->get();
        $this->classroom->setRelation('announcements', $announcements);

        // Load assignments with submissions and users — filter for students
        $assignmentsQuery = $this->classroom->assignments()->with(['user', 'submissions']);
        if (! $user->isAdmin() && ! $this->classroom->isOwnedBy($user)) {
            $assignmentsQuery->where('status', 'published');
        }
        $assignments = $assignmentsQuery->latest()->get();
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

        if (! in_array($tab, $allowedTabs, true)) {
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
        if (! $this->canManageClassroom()) {
            abort(403);
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'theme_category_id' => 'nullable|integer|exists:theme_categories,id',
        ]);

        $this->classroom->update([
            'name' => $this->name,
            'section' => $this->section,
            'description' => $this->description,
            'theme_category_id' => $this->theme_category_id,
        ]);

        $this->classroom->refresh();

        $this->dispatch('classroom-updated', [
            'id' => $this->classroom->id,
            'name' => $this->name,
            'color' => $this->classroom->themeCategory?->color ?? '#8B5CF6',
        ]);
        $this->dispatch('notify', message: __('Classroom settings saved successfully.'));
    }

    public function toggleArchive(): void
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($this->classroom->isOwnedBy($user), 403);

        $this->classroom->is_archived = ! $this->classroom->is_archived;
        $this->classroom->save();

        $this->dispatch('notify', message: $this->classroom->is_archived ? __('Classroom archived.') : __('Classroom restored.'));
    }

    public function deleteClassroom()
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($this->classroom->isOwnedBy($user), 403);

        if (trim($this->deleteConfirm) !== $this->classroom->name) {
            $this->addError('deleteConfirm', __('Please type the classroom name exactly to confirm deletion.'));

            return;
        }

        $classroomName = $this->classroom->name;
        $this->classroom->delete();

        session()->flash('message', __('Classroom ":name" was deleted.', ['name' => $classroomName]));

        return redirect()->route('classrooms');
    }

    public function confirmDeleteAnnouncement(int $id): void
    {
        $this->deleteAnnouncementId = $id;
        $this->showDeleteAnnouncementModal = true;
    }

    public function deleteAnnouncement(?int $id = null): void
    {
        $announcementId = $id ?? $this->deleteAnnouncementId;

        if (! $announcementId) {
            return;
        }

        $announcement = Announcement::findOrFail($announcementId);

        // Ensure announcement belongs to this classroom (prevent cross-classroom deletion)
        abort_unless($announcement->classroom_id === $this->classroom->id, 404);

        /** @var User $user */
        $user = Auth::user();
        if ($announcement->user_id !== $user->id && ! $this->classroom->canManageClassroom($user)) {
            abort(403);
        }

        $announcement->delete();
        $this->showDeleteAnnouncementModal = false;
        $this->deleteAnnouncementId = null;
        // Reload all relations after deletion
        $this->loadClassroomRelations();
    }

    public function deleteAssignment(int $id)
    {
        // Fix #12: removed unused $user variable
        if (! $this->canManageClassroom()) {
            abort(403);
        }

        $assignment = \App\Models\Assignment::where('classroom_id', $this->classroom->id)
            ->where('id', $id)
            ->firstOrFail();
        $assignment->delete();
    }

    public function render()
    {
        // Fix #3: no relationship loading here — all done in mount() and targeted refreshes
        $themes = \App\Models\ThemeCategory::active()->orderBy('planet_number')->get();

        return view('livewire.classroom.show', compact('themes'));
    }
}
