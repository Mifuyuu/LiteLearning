<?php

namespace App\Livewire\Admin;

use App\Models\User;
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

        $user->role = $newRole;
        $user->save();

        $this->dispatch('notify', message: __('messages.admin.user_role_updated', ['role' => ucfirst($newRole)]));
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', message: __('messages.admin.cannot_self_disable'));

            return;
        }

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
