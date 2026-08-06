<?php

namespace App\Livewire\Admin;

use App\Models\ThemeCategory;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ThemeCategories extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $editingId = null;

    public array $form = [
        'name' => '',
        'color' => '#6B3FBF',
        'is_active' => true,

        'planet_number' => 1,
    ];

    protected array $rules = [
        'form.name' => 'required|string|max:100',
        'form.color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        'form.is_active' => 'boolean',

        'form.planet_number' => 'required|integer|min:1|max:23',
    ];

    protected array $messages = [
        'form.name.required' => 'กรุณาระบุชื่อ category',
        'form.color.regex' => 'กรุณาระบุสีในรูปแบบ hex (#RRGGBB)',
        'form.planet_number.required' => 'กรุณาเลือกดาวเคราะห์',
        'form.planet_number.min' => 'หมายเลขดาวเคราะห์ต้องอยู่ระหว่าง 1-21',
        'form.planet_number.max' => 'หมายเลขดาวเคราะห์ต้องอยู่ระหว่าง 1-23',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset('form', 'editingId');
        $this->form = [
            'name' => '',
            'color' => '#6B3FBF',
            'is_active' => true,

            'planet_number' => 1,
        ];
        $this->showModal = true;
    }

    public function openEdit(ThemeCategory $category): void
    {
        $this->editingId = $category->id;
        $this->form = [
            'name' => $category->name,
            'color' => $category->color,
            'is_active' => $category->is_active,

            'planet_number' => $category->planet_number,
        ];
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = $this->form;

        if ($this->editingId) {
            $category = ThemeCategory::findOrFail($this->editingId);
            $category->update($data);
            $this->dispatch('notify', message: 'อัปเดตหมวดหมู่สำเร็จ', type: 'success');
        } else {
            ThemeCategory::create($data);
            $this->dispatch('notify', message: 'สร้างหมวดหมู่สำเร็จ', type: 'success');
        }

        $this->showModal = false;
        $this->reset('form', 'editingId');
    }

    public function delete(ThemeCategory $category): void
    {
        $category->delete();
        $this->dispatch('notify', message: 'ลบหมวดหมู่สำเร็จ', type: 'success');
    }

    public function render()
    {
        $categories = ThemeCategory::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('planet_number')
            ->paginate(7);

        return view('livewire.admin.theme-categories', ['categories' => $categories]);
    }
}
