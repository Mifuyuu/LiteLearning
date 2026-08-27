<?php

namespace App\Livewire\Admin;

use App\Models\ThemeCategory;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ThemeCategories extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public mixed $planetImageUpload = null;

    public ?string $originalPlanetKey = null;

    public array $form = [
        'name' => '',
        'color' => '#6B3FBF',
        'is_active' => true,

        'planet_key' => '',
    ];

    protected function rules(): array
    {
        $imageRequired = ! $this->editingId || $this->form['planet_key'] !== $this->originalPlanetKey;

        return [
            'form.name' => 'required|string|max:100',
            'form.color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'form.is_active' => 'boolean',

            'form.planet_key' => [
                'required',
                'string',
                'max:30',
                'regex:/^[a-z0-9_-]+$/',
                Rule::unique('theme_categories', 'planet_key')->ignore($this->editingId),
            ],
            'planetImageUpload' => ($imageRequired ? 'required' : 'nullable').'|file|mimes:svg|max:1024',
        ];
    }

    protected function messages(): array
    {
        return [
            'form.name.required' => __('messages.admin.theme_name_required'),
            'form.color.regex' => __('messages.admin.theme_color_hex'),
            'form.planet_key.required' => __('messages.admin.theme_planet_required'),
            'form.planet_key.regex' => __('messages.admin.theme_planet_key_format'),
            'form.planet_key.unique' => __('messages.admin.theme_planet_key_taken'),
            'planetImageUpload.required' => __('messages.admin.theme_planet_image_required'),
            'planetImageUpload.mimes' => __('messages.admin.theme_planet_image_svg'),
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset('form', 'editingId', 'planetImageUpload');
        $this->originalPlanetKey = null;
        $this->form = [
            'name' => '',
            'color' => '#6B3FBF',
            'is_active' => true,

            'planet_key' => '',
        ];
        $this->showModal = true;
    }

    public function openEdit(ThemeCategory $category): void
    {
        $this->editingId = $category->id;
        $this->originalPlanetKey = $category->planet_key;
        $this->planetImageUpload = null;
        $this->form = [
            'name' => $category->name,
            'color' => $category->color,
            'is_active' => $category->is_active,

            'planet_key' => $category->planet_key,
        ];
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = $this->form;

        if ($this->planetImageUpload) {
            $this->planetImageUpload->storeAs('', "planet_{$data['planet_key']}.svg", ['disk' => 'planets']);
        }

        if ($this->editingId) {
            $category = ThemeCategory::findOrFail($this->editingId);
            $category->update($data);
            $this->dispatch('notify', message: __('messages.admin.theme_updated'), type: 'success');
        } else {
            ThemeCategory::create($data);
            $this->dispatch('notify', message: __('messages.admin.theme_created'), type: 'success');
        }

        $this->showModal = false;
        $this->reset('form', 'editingId', 'planetImageUpload');
    }

    public function toggleActive(ThemeCategory $category): void
    {
        $category->is_active = ! $category->is_active;
        $category->save();

        $this->dispatch('notify', message: __('messages.admin.theme_status_updated'), type: 'success');
    }

    public function delete(ThemeCategory $category): void
    {
        $category->delete();
        $this->dispatch('notify', message: __('messages.admin.theme_deleted'), type: 'success');
    }

    public function render()
    {
        $categories = ThemeCategory::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('planet_key')
            ->paginate(7);

        return view('livewire.admin.theme-categories', ['categories' => $categories]);
    }
}
