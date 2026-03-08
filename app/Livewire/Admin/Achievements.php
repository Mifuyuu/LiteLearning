<?php

namespace App\Livewire\Admin;

use App\Models\Achievement;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Achievements extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public $badgeImageUpload = null;
    public $showModal = false;
    public $editingId = null;

    public $form = [
        'code' => '',
        'name' => '',
        'description' => '',
        'badge_image' => 'images/achievements/achievements-img-01.svg',
        'coin_reward' => 50,
        'xp_reward' => 100,
        'is_active' => true,
    ];
    protected $rules = [
        'form.code' => 'required|string|max:100',
        'form.name' => 'required|string|max:255',
        'form.description' => 'nullable|string',
        'form.badge_image' => 'nullable|string|max:255',
        'form.coin_reward' => 'required|integer|min:0',
        'form.xp_reward' => 'required|integer|min:0',
        'form.is_active' => 'boolean',
        'badgeImageUpload' => 'nullable|file|mimes:png,svg|max:2048',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->editingId = null;
        $this->badgeImageUpload = null;
        $this->form = ['code' => '', 'name' => '', 'description' => '', 'badge_image' => 'images/achievements/achievements-img-01.svg', 'coin_reward' => 50, 'xp_reward' => 100, 'is_active' => true];
        $this->showModal = true;
    }

    public function openEdit(Achievement $achievement)
    {
        $this->editingId = $achievement->id;
        $this->form = [
            'code' => $achievement->code,
            'name' => $achievement->name,
            'description' => $achievement->description,
            'badge_image' => $achievement->badge_image ?? 'images/achievements/achievements-img-01.svg',
            'coin_reward' => $achievement->coin_reward,
            'xp_reward' => $achievement->xp_reward,
            'is_active' => $achievement->is_active,
        ];
        $this->showModal = true;
        $this->badgeImageUpload = null;
    }

    public function save(): void
    {
        $this->validate();

        // Handle file upload
        if ($this->badgeImageUpload) {
            $filename = $this->badgeImageUpload->getClientOriginalName();
            $this->badgeImageUpload->storeAs('', $filename, ['disk' => 'achievements']);
            $this->form['badge_image'] = 'images/achievements/' . $filename;
        }

        $data = $this->form;

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
