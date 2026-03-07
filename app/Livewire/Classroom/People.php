<?php

namespace App\Livewire\Classroom;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class People extends Component
{
    #[Locked]
    public Classroom $classroom;
    public string $inviteEmail = '';
    public string $inviteCoTeacherEmail = '';

    public function mount(Classroom $classroom)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$classroom->hasAccess($user)) {
            abort(403);
        }

        $this->classroom = $classroom;
    }

    public function addCoTeacher()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->classroom->isOwnedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        $this->validate(['inviteCoTeacherEmail' => 'required|email']);

        $target = User::where('email', $this->inviteCoTeacherEmail)->first();

        if (!$target) {
            $this->addError('inviteCoTeacherEmail', __('ไม่พบผู้ใช้งานนี้ในระบบ'));
            return;
        }

        if ($this->classroom->isOwnedBy($target)) {
            $this->addError('inviteCoTeacherEmail', __('ผู้ใช้นี้เป็นเจ้าของห้องอยู่แล้ว'));
            return;
        }

        if (!$target->isTeacher() && !$target->isAdmin()) {
            $this->addError('inviteCoTeacherEmail', __('สามารถเพิ่ม Co-Teacher ได้เฉพาะบัญชีอาจารย์เท่านั้น'));
            return;
        }

        // If already a co-teacher, skip
        if ($this->classroom->isCoTeacher($target)) {
            $this->addError('inviteCoTeacherEmail', __('ผู้ใช้นี้เป็น Co-Teacher อยู่แล้ว'));
            return;
        }

        // Detach if already a member in another role, then attach as co-teacher
        $this->classroom->members()->detach($target->id);
        $this->classroom->members()->attach($target->id, [
            'role' => 'co-teacher',
            'joined_at' => now(),
        ]);

        $this->reset('inviteCoTeacherEmail');
        $this->classroom->refresh();
        $this->dispatch('notify', message: __('เพิ่ม Co-Teacher เรียบร้อยแล้ว'));
    }

    public function removeCoTeacher(int $userId)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->classroom->isOwnedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        $this->classroom->members()->detach($userId);
        $this->classroom->refresh();
        $this->dispatch('notify', message: __('ลบ Co-Teacher เรียบร้อยแล้ว'));
    }

    public function removeMember(int $userId)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->classroom->canManageClassroom($user)) {
            abort(403);
        }

        // Co-teachers cannot remove the owner
        if ($userId === $this->classroom->teacher_id) {
            abort(403);
        }

        $this->classroom->members()->detach($userId);
        $this->classroom->refresh();

        $this->dispatch('notify', message: __('Member removed successfully.'));
    }

    public function removeAllMembers()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$this->classroom->isOwnedBy($user) && !$user->isAdmin()) {
            abort(403);
        }

        // Only detach students, not co-teachers
        $this->classroom->students()->detach();
        $this->classroom->refresh();

        $this->dispatch('notify', message: __('All students removed successfully.'));
    }

    public function render()
    {
        $this->classroom->load(['teacher', 'members', 'coTeachers']);

        return view('livewire.classroom.people');
    }
}
