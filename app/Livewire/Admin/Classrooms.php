<?php

namespace App\Livewire\Admin;

use App\Models\Classroom;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Classrooms extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteClassroom(string $slug)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $classroom = Classroom::withTrashed()->where('slug', $slug)->firstOrFail();
        $classroom->forceDelete();
        $this->dispatch('notify', message: __('messages.admin.classroom_deleted'));
    }

    public function setArchived(string $slug, bool $archived)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $classroom = Classroom::withTrashed()->where('slug', $slug)->firstOrFail();
        $classroom->is_archived = $archived;
        $classroom->save();
        $this->dispatch('notify', message: __('messages.admin.classroom_status_updated'));
    }

    public function restoreClassroom(string $slug)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $classroom = Classroom::onlyTrashed()->where('slug', $slug)->firstOrFail();
        $classroom->restore();
        $this->dispatch('notify', message: __('messages.admin.classroom_restored'));
    }

    public function render()
    {
        $query = Classroom::withTrashed()->with(['teacher', 'members', 'themeCategory']);

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('code', 'like', '%'.$this->search.'%');
        }

        if ($this->statusFilter === 'archived') {
            $query->where('is_archived', true);
        } elseif ($this->statusFilter === 'active') {
            $query->where('is_archived', false);
        }

        return view('livewire.admin.classrooms', [
            'classrooms' => $query->latest()->paginate(15),
        ]);
    }
}
