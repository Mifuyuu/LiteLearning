<?php

namespace App\Livewire\Classroom;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Show extends Component
{
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.classroom-show', $params);
    }
    #[Locked]
    public Classroom $classroom;

    public function mount(Classroom $classroom): void
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($classroom->hasAccess($user), 404);

        $this->classroom = $classroom;
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

        $materialsQuery = $this->classroom->materials()->with(['classworkItem.user', 'classworkItem.topic', 'attachments']);
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
            'sort' => 'sort-first-name',
        ]), navigate: true);
    }

    public function routeToGradebook(): void
    {
        abort_unless($this->classroom->canManageClassroom(Auth::user()), 403);

        $this->redirect(route('classroom.gradebook', $this->classroom), navigate: true);
    }

    protected function canManageClassroom(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->classroom->canManageClassroom($user);
    }

    private function visibleSubmissionAssignments(): Collection
    {
        return $this->classroom->assignments
            ->whereNotIn('type', ['announcement', 'material', 'topic'])
            ->values();
    }

    private function pendingAssignments(Collection $assignments): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        if ($this->classroom->canManageClassroom($user)) {
            $studentCount = $this->classroom->students->count();

            return $assignments->filter(
                fn (Assignment $assignment): bool => $assignment->submittedCount() < $studentCount
            )->values();
        }

        return $assignments->filter(function (Assignment $assignment) use ($user): bool {
            $submission = $assignment->submissions->firstWhere('user_id', $user->id);

            return ! $submission?->isTurnedIn();
        })->values();
    }

    private function completedAssignments(Collection $assignments): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        if ($this->classroom->canManageClassroom($user)) {
            $studentCount = $this->classroom->students->count();

            return $assignments->filter(
                fn (Assignment $assignment): bool => $studentCount > 0 && $assignment->submittedCount() >= $studentCount
            )->values();
        }

        return $assignments->filter(function (Assignment $assignment) use ($user): bool {
            $submission = $assignment->submissions->firstWhere('user_id', $user->id);

            return (bool) $submission?->isTurnedIn();
        })->values();
    }

    private function lastNameKey(string $name): string
    {
        $parts = preg_split('/\s+/u', trim($name)) ?: [$name];

        return mb_strtolower((string) end($parts));
    }

    private function sortUsers(Collection $users): Collection
    {
        return $users
            ->sortBy(fn (User $user) => $this->lastNameKey($user->name).'|'.mb_strtolower($user->name))
            ->values();
    }

    public function render()
    {
        $assignments = $this->visibleSubmissionAssignments();

        return view('livewire.classroom.show', [
            'assignmentCount' => $assignments->count(),
            'coTeachers' => $this->sortUsers($this->classroom->coTeachers),
            'completedAssignments' => $this->completedAssignments($assignments),
            'pendingAssignments' => $this->pendingAssignments($assignments),
            'students' => $this->sortUsers($this->classroom->students),
        ])->title($this->classroom->name);
    }
}
