<?php

namespace App\Livewire\Admin;

use App\Models\Badge;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Badges extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;

    public $form = [
        'code' => '',
        'name' => '',
        'description' => '',
        'icon' => 'fas fa-id-badge',
        'color' => '#6366f1',
        'target_role' => '',
    ];

    protected $rules = [
        'form.code' => 'required|string|max:100',
        'form.name' => 'required|string|max:255',
        'form.description' => 'nullable|string',
        'form.icon' => 'nullable|string|max:100',
        'form.color' => 'required|string|max:20',
        'form.target_role' => 'nullable|in:,student,teacher',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->editingId = null;
        $this->form = ['code' => '', 'name' => '', 'description' => '', 'icon' => 'fas fa-id-badge', 'color' => '#6366f1', 'target_role' => ''];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(Badge $badge)
    {
        $this->editingId = $badge->id;
        $this->form = [
            'code' => $badge->code,
            'name' => $badge->name,
            'description' => $badge->description,
            'icon' => $badge->icon,
            'color' => $badge->color,
            'target_role' => $badge->target_role ?? '',
        ];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = $this->form;
        $data['target_role'] = $data['target_role'] ?: null;

        if ($this->editingId) {
            $badge = Badge::findOrFail($this->editingId);
            $badge->update($data);
            $this->dispatch('notify', message: __('admin.badges.updated'));
        } else {
            Badge::create($data);
            $this->dispatch('notify', message: __('admin.badges.created'));
        }

        $this->showModal = false;
    }

    public function delete(Badge $badge)
    {
        $badge->delete();
        $this->dispatch('notify', message: __('admin.badges.deleted'));
    }

    public function render()
    {
        $query = Badge::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
        }

        return view('livewire.admin.badges', [
            'badges' => $query->latest()->paginate(10),
        ]);
    }
}
