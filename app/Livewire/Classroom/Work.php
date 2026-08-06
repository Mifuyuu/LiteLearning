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
class Work extends Component
{
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.classroom-work', $params);
    }
    #[Locked]
    public Classroom $classroom;

    public string $scope = 'all';

    public function mount(Classroom $classroom, string $scope = 'all'): void
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($classroom->hasAccess($user), 404);

        $this->classroom = $classroom;
        $this->scope = $this->normalizeScope($scope);
        $this->loadRelations();
    }

    public function hydrate(): void
    {
        $this->loadRelations();
    }

    private function loadRelations(): void
    {
        $this->classroom->load(['teacher', 'students', 'topics']);
    }

    private function normalizeScope(string $scope): string
    {
        return in_array($scope, ['all', 'pending', 'completed'], true) ? $scope : 'all';
    }

    private function visibleAssignments(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        $assignments = $this->classroom->assignments()
            ->with(['classworkItem.topic', 'classworkItem.user', 'submissions'])
            ->whereNotIn('assignments.type', ['announcement', 'material', 'topic'])
            ->latest()
            ->get();

        if (! $this->classroom->canManageClassroom($user)) {
            $assignments = $assignments
                ->where('status', 'published')
                ->filter(function (Assignment $assignment): bool {
                    return ! $assignment->classworkItem?->published_at?->isFuture();
                })
                ->values();
        }

        return $assignments->values();
    }

    private function pendingAssignments(Collection $assignments): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        if ($this->classroom->canManageClassroom($user)) {
            $studentCount = $this->classroom->students()->count();

            return $assignments->filter(function (Assignment $assignment) use ($studentCount): bool {
                return $assignment->submittedCount() < $studentCount;
            })->values();
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
            $studentCount = $this->classroom->students()->count();

            return $assignments->filter(function (Assignment $assignment) use ($studentCount): bool {
                return $studentCount > 0 && $assignment->submittedCount() >= $studentCount;
            })->values();
        }

        return $assignments->filter(function (Assignment $assignment) use ($user): bool {
            $submission = $assignment->submissions->firstWhere('user_id', $user->id);

            return (bool) $submission?->isTurnedIn();
        })->values();
    }

    public function render()
    {
        $assignments = $this->visibleAssignments();
        $pendingAssignments = $this->pendingAssignments($assignments);
        $completedAssignments = $this->completedAssignments($assignments);

        if ($this->scope === 'pending') {
            $completedAssignments = collect();
        }

        if ($this->scope === 'completed') {
            $pendingAssignments = collect();
        }

        return view('livewire.classroom.work', [
            'pendingAssignments' => $pendingAssignments,
            'completedAssignments' => $completedAssignments,
            'assignmentCount' => $assignments->count(),
        ])->title($this->classroom->name.' - '.'งานในชั้นเรียน');
    }
}
