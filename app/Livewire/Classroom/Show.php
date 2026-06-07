<?php

namespace App\Livewire\Classroom;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\ThemeCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    #[Locked]
    public Classroom $classroom;

    public string $name = '';

    public string $section = '';

    public string $description = '';

    public ?int $theme_category_id = null;

    public string $deleteConfirm = '';

    public bool $showDeleteAnnouncementModal = false;

    public ?int $deleteAnnouncementId = null;

    protected function canEditSettings(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->classroom->isOwnedBy($user) || $user->isAdmin();
    }

    private function deleteAssignmentRecord(Assignment $assignment): void
    {
        DB::transaction(function () use ($assignment): void {
            foreach ($assignment->attachments as $attachment) {
                Storage::disk('s3')->delete($attachment->file_path);
                $attachment->delete();
            }

            $assignment->comments()->delete();

            foreach ($assignment->submissions()->with('attachments')->get() as $submission) {
                foreach ($submission->attachments as $attachment) {
                    Storage::disk('s3')->delete($attachment->file_path);
                    $attachment->delete();
                }
            }

            $assignment->classworkItem?->delete();
        });
    }

    public function mount(Classroom $classroom): void
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($classroom->hasAccess($user), 404);

        $this->classroom = $classroom;
        $this->name = $classroom->name;
        $this->section = $classroom->section ?? '';
        $this->description = $classroom->description ?? '';
        $this->theme_category_id = $classroom->theme_category_id;

        $this->loadClassroomRelations();
    }

    private function loadClassroomRelations(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->classroom->load([
            'teacher',
            'students',
            'coTeachers',
            'topics',
        ]);

        $announcementsQuery = $this->classroom->announcements()->with(['classworkItem.user', 'comments.user']);
        if (! $this->classroom->canManageClassroom($user)) {
            $announcementsQuery->where(function ($query): void {
                $query->whereNull('classwork_items.published_at')
                    ->orWhere('classwork_items.published_at', '<=', now());
            });
        }
        $this->classroom->setRelation('announcements', $announcementsQuery->latest()->get());

        $assignmentsQuery = $this->classroom->assignments()->with(['classworkItem.user', 'classworkItem.topic', 'submissions']);
        if (! $this->classroom->canManageClassroom($user)) {
            $assignmentsQuery->where('assignments.status', 'published');
        }

        $assignments = $assignmentsQuery->latest()->get();
        if ($user->isStudent()) {
            $assignments = $assignments->filter(function (Assignment $assignment) {
                if ($assignment->classworkItem?->published_at?->isFuture()) {
                    return false;
                }

                return $assignment->status !== 'scheduled';
            })->values();
        }
        $this->classroom->setRelation('assignments', $assignments);

        $materialsQuery = $this->classroom->materials()->with(['classworkItem.user', 'classworkItem.topic']);
        if (! $this->classroom->canManageClassroom($user)) {
            $materialsQuery->where(function ($query): void {
                $query->whereNull('classwork_items.published_at')
                    ->orWhere('classwork_items.published_at', '<=', now());
            });
        }
        $this->classroom->setRelation('materials', $materialsQuery->latest()->get());
    }

    public function routeToWork(): void
    {
        $this->redirect(route('classroom.work', [
            'classroom' => $this->classroom,
            'scope' => 'all',
        ]), navigate: true);
    }

    public function setTab(string $tab): void
    {
        if ($tab === 'classwork') {
            $this->routeToWork();

            return;
        }

        if ($tab === 'people') {
            $this->routeToRoster();

            return;
        }

        if ($tab === 'grades') {
            $this->routeToGradebook();
        }
    }

    public function routeToRoster(): void
    {
        $this->redirect(route('classroom.roster', [
            'classroom' => $this->classroom,
            'sort' => 'sort-last-name',
        ]), navigate: true);
    }

    public function routeToGradebook(): void
    {
        abort_unless($this->classroom->canManageClassroom(Auth::user()), 403);

        $this->redirect(route('classroom.gradebook', [
            'classroom' => $this->classroom,
            'sort' => 'sort-last-name',
            'display' => 'default',
        ]), navigate: true);
    }

    public function overviewTopics(): array
    {
        $groups = [];

        foreach ($this->classroom->topics as $topic) {
            $items = collect()
                ->merge(
                    $this->classroom->assignments
                        ->where('topic_id', $topic->id)
                        ->map(fn (Assignment $assignment) => [
                            'kind' => 'assignment',
                            'model' => $assignment,
                            'created_at' => $assignment->created_at,
                        ])
                )
                ->merge(
                    $this->classroom->materials
                        ->where('topic_id', $topic->id)
                        ->map(fn ($material) => [
                            'kind' => 'material',
                            'model' => $material,
                            'created_at' => $material->created_at,
                        ])
                )
                ->sortByDesc('created_at')
                ->values();

            if ($items->isNotEmpty()) {
                $groups[] = [
                    'name' => $topic->name,
                    'items' => $items,
                ];
            }
        }

        $general = collect()
            ->merge(
                $this->classroom->assignments
                    ->whereNull('topic_id')
                    ->map(fn (Assignment $assignment) => [
                        'kind' => 'assignment',
                        'model' => $assignment,
                        'created_at' => $assignment->created_at,
                    ])
            )
            ->merge(
                $this->classroom->materials
                    ->whereNull('topic_id')
                    ->map(fn ($material) => [
                        'kind' => 'material',
                        'model' => $material,
                        'created_at' => $material->created_at,
                    ])
            )
            ->sortByDesc('created_at')
            ->values();

        if ($general->isNotEmpty()) {
            $groups[] = [
                'name' => __('General'),
                'items' => $general,
            ];
        }

        return $groups;
    }

    protected function canManageClassroom(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->classroom->canManageClassroom($user);
    }

    public function saveSettings()
    {
        if (! $this->canEditSettings()) {
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
        $this->loadClassroomRelations();

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
        abort_unless($announcement->classroom_id === $this->classroom->id, 404);

        /** @var User $user */
        $user = Auth::user();
        if ($announcement->user_id !== $user->id && ! $this->classroom->canManageClassroom($user)) {
            abort(403);
        }

        DB::transaction(function () use ($announcement): void {
            foreach ($announcement->attachments as $attachment) {
                Storage::disk('s3')->delete($attachment->file_path);
                $attachment->delete();
            }

            $announcement->comments()->delete();
            $announcement->classworkItem?->delete();
        });

        $this->showDeleteAnnouncementModal = false;
        $this->deleteAnnouncementId = null;
        $this->loadClassroomRelations();
    }

    public function deleteAssignment(int $id)
    {
        if (! $this->canManageClassroom()) {
            abort(403);
        }

        $assignment = Assignment::whereHas(
            'classworkItem',
            fn ($query) => $query->where('classroom_id', $this->classroom->id)
        )
            ->where('id', $id)
            ->firstOrFail();

        $this->deleteAssignmentRecord($assignment);
        $this->loadClassroomRelations();
    }

    public function render()
    {
        return view('livewire.classroom.show', [
            'themes' => ThemeCategory::active()->orderBy('planet_number')->get(),
            'topicGroups' => $this->overviewTopics(),
        ])->title($this->classroom->name);
    }
}
