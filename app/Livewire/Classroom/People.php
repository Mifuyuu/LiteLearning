<?php

namespace App\Livewire\Classroom;

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
class People extends Component
{
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.people', $params);
    }
    #[Locked]
    public Classroom $classroom;

    public string $inviteCoTeacherEmail = '';

    public string $sort = 'sort-first-name';

    public function mount(Classroom $classroom, string $sort = 'sort-first-name'): void
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($classroom->hasAccess($user), 404);

        $this->classroom = $classroom;
        $this->sort = $this->normalizeSort($sort);
        $this->loadRelations();
    }

    public function hydrate(): void
    {
        $this->loadRelations();
    }

    private function loadRelations(): void
    {
        $this->classroom->load(['teacher', 'coTeachers', 'students']);
    }

    private function normalizeSort(string $sort): string
    {
        return in_array($sort, ['sort-first-name', 'sort-newest'], true)
            ? $sort
            : 'sort-first-name';
    }

    private function sortUsers(Collection $users): Collection
    {
        $sorted = match ($this->sort) {
            'sort-newest' => $users->sortByDesc(fn (User $user) => $user->pivot?->joined_at ?? $user->created_at),
            default => $users->sortBy(fn (User $user) => mb_strtolower($user->name)),
        };

        return $sorted->values();
    }

    public function addCoTeacher()
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $this->classroom->isOwnedBy($user) && ! $user->isAdmin()) {
            abort(403);
        }

        $this->validate(['inviteCoTeacherEmail' => 'required|email'], [
            'inviteCoTeacherEmail.required' => __('messages.validation.email'),
        ]);

        $target = User::where('email', $this->inviteCoTeacherEmail)->first();

        if (! $target) {
            $this->addError('inviteCoTeacherEmail', __('messages.classroom.user_not_found'));

            return;
        }

        if ($this->classroom->isOwnedBy($target)) {
            $this->addError('inviteCoTeacherEmail', __('messages.classroom.already_owner'));

            return;
        }

        if (! $target->isTeacher() && ! $target->isAdmin()) {
            $this->addError('inviteCoTeacherEmail', __('messages.classroom.teacher_only'));

            return;
        }

        if ($this->classroom->isCoTeacher($target)) {
            $this->addError('inviteCoTeacherEmail', __('messages.classroom.already_co_teacher'));

            return;
        }

        $this->classroom->members()->detach($target->id);
        $this->classroom->members()->attach($target->id, [
            'role' => 'co-teacher',
            'joined_at' => now(),
        ]);

        $this->reset('inviteCoTeacherEmail');
        $this->dispatch('notify', message: __('messages.classroom.co_teacher_added'));
    }

    public function removeCoTeacher(int $userId)
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $this->classroom->isOwnedBy($user) && ! $user->isAdmin()) {
            abort(403);
        }

        $this->classroom->members()->detach($userId);
        $this->dispatch('notify', message: __('messages.classroom.co_teacher_removed'));
    }

    public function removeMember(int $userId)
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $this->classroom->canManageClassroom($user)) {
            abort(403);
        }

        if ($userId === $this->classroom->teacher_id) {
            abort(403);
        }

        $this->classroom->members()->detach($userId);
        $this->dispatch('notify', message: __('messages.classroom.member_removed'));
    }

    public function removeAllMembers()
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $this->classroom->isOwnedBy($user) && ! $user->isAdmin()) {
            abort(403);
        }

        $this->classroom->students()->detach();
        $this->dispatch('notify', message: __('messages.classroom.all_students_removed'));
    }

    public function render()
    {
        return view('livewire.classroom.people', [
            'coTeachers' => $this->sortUsers($this->classroom->coTeachers),
            'students' => $this->sortUsers($this->classroom->students),
        ])->title($this->classroom->name.' - '.'สมาชิก');
    }
}
