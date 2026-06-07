<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class People extends Component
{
    #[Locked]
    public Classroom $classroom;

    public string $inviteCoTeacherEmail = '';

    public string $sort = 'sort-last-name';

    public function mount(Classroom $classroom, string $sort = 'sort-last-name'): void
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
        return in_array($sort, ['sort-first-name', 'sort-last-name', 'sort-newest'], true)
            ? $sort
            : 'sort-last-name';
    }

    private function lastNameKey(string $name): string
    {
        $parts = preg_split('/\s+/u', trim($name)) ?: [$name];

        return mb_strtolower((string) end($parts));
    }

    private function sortUsers(Collection $users): Collection
    {
        $sorted = match ($this->sort) {
            'sort-newest' => $users->sortByDesc(fn (User $user) => $user->pivot?->joined_at ?? $user->created_at),
            'sort-first-name' => $users->sortBy(fn (User $user) => mb_strtolower($user->name)),
            default => $users->sortBy(fn (User $user) => $this->lastNameKey($user->name).'|'.mb_strtolower($user->name)),
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

        $this->validate(['inviteCoTeacherEmail' => 'required|email']);

        $target = User::where('email', $this->inviteCoTeacherEmail)->first();

        if (! $target) {
            $this->addError('inviteCoTeacherEmail', __('ไม่พบผู้ใช้งานนี้ในระบบ'));

            return;
        }

        if ($this->classroom->isOwnedBy($target)) {
            $this->addError('inviteCoTeacherEmail', __('ผู้ใช้นี้เป็นเจ้าของห้องอยู่แล้ว'));

            return;
        }

        if (! $target->isTeacher() && ! $target->isAdmin()) {
            $this->addError('inviteCoTeacherEmail', __('สามารถเพิ่ม Co-Teacher ได้เฉพาะบัญชีอาจารย์เท่านั้น'));

            return;
        }

        if ($this->classroom->isCoTeacher($target)) {
            $this->addError('inviteCoTeacherEmail', __('ผู้ใช้นี้เป็น Co-Teacher อยู่แล้ว'));

            return;
        }

        $this->classroom->members()->detach($target->id);
        $this->classroom->members()->attach($target->id, [
            'role' => 'co-teacher',
            'joined_at' => now(),
        ]);

        $this->reset('inviteCoTeacherEmail');
        $this->dispatch('notify', message: __('เพิ่ม Co-Teacher เรียบร้อยแล้ว'));
    }

    public function removeCoTeacher(int $userId)
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $this->classroom->isOwnedBy($user) && ! $user->isAdmin()) {
            abort(403);
        }

        $this->classroom->members()->detach($userId);
        $this->dispatch('notify', message: __('ลบ Co-Teacher เรียบร้อยแล้ว'));
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
        $this->dispatch('notify', message: __('Member removed successfully.'));
    }

    public function removeAllMembers()
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $this->classroom->isOwnedBy($user) && ! $user->isAdmin()) {
            abort(403);
        }

        $this->classroom->students()->detach();
        $this->dispatch('notify', message: __('All students removed successfully.'));
    }

    public function render()
    {
        return view('livewire.classroom.people', [
            'coTeachers' => $this->sortUsers($this->classroom->coTeachers),
            'students' => $this->sortUsers($this->classroom->students),
        ])->title($this->classroom->name.' - '.__('People'));
    }
}
