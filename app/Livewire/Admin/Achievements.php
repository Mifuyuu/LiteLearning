<?php

namespace App\Livewire\Admin;

use App\Models\Achievement;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Achievements extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;

    public $form = [
        'code' => '',
        'name' => '',
        'description' => '',
        'icon' => 'fas fa-award',
        'coin_reward' => 50,
        'xp_reward' => 100,
        'target_role' => '',
        'is_active' => true,
    ];

    protected $rules = [
        'form.code' => 'required|string|max:100',
        'form.name' => 'required|string|max:255',
        'form.description' => 'nullable|string',
        'form.icon' => 'nullable|string|max:100',
        'form.coin_reward' => 'required|integer|min:0',
        'form.xp_reward' => 'required|integer|min:0',
        'form.target_role' => 'nullable|in:,student,teacher',
        'form.is_active' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->editingId = null;
        $this->form = ['code' => '', 'name' => '', 'description' => '', 'icon' => 'fas fa-award', 'coin_reward' => 50, 'xp_reward' => 100, 'target_role' => '', 'is_active' => true];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(Achievement $achievement)
    {
        $this->editingId = $achievement->id;
        $this->form = [
            'code' => $achievement->code,
            'name' => $achievement->name,
            'description' => $achievement->description,
            'icon' => $achievement->icon,
            'coin_reward' => $achievement->coin_reward,
            'xp_reward' => $achievement->xp_reward,
            'target_role' => $achievement->target_role ?? '',
            'is_active' => $achievement->is_active,
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
            $achievement = Achievement::findOrFail($this->editingId);
            $achievement->update($data);
            $this->dispatch('notify', message: __('admin.achievements.updated'));
        } else {
            Achievement::create($data);
            $this->dispatch('notify', message: __('admin.achievements.created'));
        }

        $this->showModal = false;
    }

    public function toggleActive(Achievement $achievement)
    {
        $achievement->is_active = !$achievement->is_active;
        $achievement->save();

        $this->dispatch('notify', message: __('admin.achievements.status_updated'));
    }

    public function delete(Achievement $achievement)
    {
        $achievement->delete();
        $this->dispatch('notify', message: __('admin.achievements.deleted'));
    }

    public function render()
    {
        $query = Achievement::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
        }

        return view('livewire.admin.achievements', [
            'achievements' => $query->latest()->paginate(10),
        ]);
    }
}
