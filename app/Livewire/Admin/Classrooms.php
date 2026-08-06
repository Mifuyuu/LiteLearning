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

    public function deleteClassroom(Classroom $classroom)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $classroom->delete();
        $this->dispatch('notify', message: 'ลบห้องเรียนแล้ว');
    }

    public function render()
    {
        $query = Classroom::query()->with(['teacher', 'members', 'themeCategory']);

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
