<?php

namespace App\Livewire\Admin;

use App\Exceptions\GamificationException;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Users extends Component
{
    use WithPagination;

    public $search = '';

    public $roleFilter = '';

    public $statusFilter = '';

    protected $updatesQueryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->validateOnly('search', ['search' => 'string|max:100']);
        $this->resetPage();
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', message: __('messages.admin.cannot_self_disable'));

            return;
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        AuditLog::record(
            $user->is_active ? 'user_enabled' : 'user_disabled',
            ($user->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน')." ผู้ใช้ {$user->name} ({$user->email})"
        );

        $this->dispatch('notify', message: __('messages.admin.user_status_updated'));
    }

    public function updateRole(User $user, $newRole)
    {
        if (! in_array($newRole, ['admin', 'teacher', 'student'], true)) {
            $this->dispatch('notify', message: __('messages.admin.role_invalid'));

            return;
        }

        if ($user->id === auth()->id() && $newRole !== 'admin') {
            $this->dispatch('notify', message: __('messages.admin.cannot_self_demote'));

            return;
        }

        $oldRole = $user->role;
        $user->role = $newRole;
        $user->save();

        AuditLog::record('user_role_changed', "เปลี่ยนบทบาทของ {$user->name} ({$user->email}) จาก {$oldRole} เป็น {$newRole}");

        $this->dispatch('notify', message: __('messages.admin.user_role_updated', ['role' => ucfirst($newRole)]));
    }

    public function updateUser(User $user, GamificationService $gamification, $name, $bio, $coins, $xp)
    {
        Validator::make(compact('name', 'bio', 'coins', 'xp'), [
            'name' => 'required|string|max:'.User::NAME_MAX_LENGTH,
            'bio' => 'nullable|string|max:500',
            'coins' => 'integer|min:0',
            'xp' => 'integer|min:0',
        ])->validate();

        $user->name = trim($name);
        $user->bio = $bio !== '' ? $bio : null;
        $user->save();

        if ($user->isStudent()) {
            try {
                $gamification->adminSetCoinsAndXp($user, (int) $coins, (int) $xp);
            } catch (GamificationException $e) {
                $this->dispatch('notify', message: $e->getMessage());

                return;
            }
        }

        AuditLog::record('user_updated', "แก้ไขข้อมูลผู้ใช้ {$user->name} ({$user->email})");

        $this->dispatch('notify', message: __('messages.admin.user_updated'));
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', message: __('messages.admin.cannot_self_disable'));

            return;
        }

        AuditLog::record('user_deleted', "ลบผู้ใช้ {$user->name} ({$user->email})");

        $user->delete();
        $this->dispatch('notify', message: __('messages.admin.user_deleted'));
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active' ? 1 : 0);
        }

        return view('livewire.admin.users', [
            'users' => $query->withSum('attachments', 'file_size')->latest()->paginate(15),
        ]);
    }
}
