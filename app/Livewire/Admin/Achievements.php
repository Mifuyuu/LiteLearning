<?php

namespace App\Livewire\Admin;

use App\Models\Achievement;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Achievements extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public mixed $badgeImageUpload = null;

    public bool $showModal = false;

    public ?int $editingId = null;

    public $form = [
        'code' => '',
        'name' => '',
        'description' => '',
        'badge_image' => 'images/achievements/Achievements_Novice.png',
        'coin_reward' => 50,
        'xp_reward' => 100,
        'is_active' => true,
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->badgeImageUpload = null;
        $this->form = ['code' => '', 'name' => '', 'description' => '', 'badge_image' => 'images/achievements/Achievements_Novice.png', 'coin_reward' => 50, 'xp_reward' => 100, 'is_active' => true];
        $this->showModal = true;
    }

    public function openEdit(Achievement $achievement): void
    {
        $this->editingId = $achievement->id;
        $this->form = [
            'code' => $achievement->code,
            'name' => $achievement->name,
            'description' => $achievement->description,
            'badge_image' => $achievement->badge_image ?? 'images/achievements/Achievements_Novice.png',
            'coin_reward' => $achievement->coin_reward,
            'xp_reward' => $achievement->xp_reward,
            'is_active' => $achievement->is_active,
        ];
        $this->showModal = true;
        $this->badgeImageUpload = null;
    }

    public function save(): void
    {
        $this->validate($this->rules());

        if ($this->badgeImageUpload) {
            $filename = $this->badgeImageUpload->hashName();
            $this->badgeImageUpload->storeAs('', $filename, ['disk' => 'achievements']);
            $this->form['badge_image'] = 'images/achievements/'.$filename;
        }

        $data = $this->form;

        if ($this->editingId) {
            $achievement = Achievement::findOrFail($this->editingId);
            unset($data['code']);
            $achievement->update($data);
            $this->dispatch('notify', message: __('messages.admin.achievement_updated'));
        } else {
            Achievement::create($data);
            $this->dispatch('notify', message: __('messages.admin.achievement_created'));
        }

        $this->showModal = false;
    }

    public function toggleActive(Achievement $achievement): void
    {
        $achievement->is_active = ! $achievement->is_active;
        $achievement->save();

        $this->dispatch('notify', message: __('messages.admin.achievement_status_updated'));
    }

    public function delete(Achievement $achievement): void
    {
        $achievement->delete();
        $this->dispatch('notify', message: __('messages.admin.achievement_deleted'));
    }

    public function render()
    {
        $query = Achievement::query();

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('code', 'like', '%'.$this->search.'%');
        }

        return view('livewire.admin.achievements', [
            'achievements' => $query->latest()->paginate(10),
        ]);
    }

    protected function messages(): array
    {
        return [
            'form.code.required' => __('messages.validation.code_achievement'),
            'form.name.required' => __('messages.validation.name_achievement'),
            'form.coin_reward.required' => __('messages.validation.coin_reward'),
            'form.xp_reward.required' => __('messages.validation.xp_reward'),
        ];
    }

    protected function rules(): array
    {
        return [
            'form.code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('achievements', 'code')->ignore($this->editingId),
            ],
            'form.name' => 'required|string|max:255',
            'form.description' => 'nullable|string',
            'form.badge_image' => 'nullable|string|max:255',
            'form.coin_reward' => 'required|integer|min:0',
            'form.xp_reward' => 'required|integer|min:0',
            'form.is_active' => 'boolean',
            'badgeImageUpload' => 'nullable|file|mimes:png,svg,webp|max:2048',
        ];
    }
}
