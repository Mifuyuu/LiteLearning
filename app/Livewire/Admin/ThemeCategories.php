<?php

namespace App\Livewire\Admin;

use App\Models\ClassroomThemeCategory;
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
        'name'          => '',
        'description'   => '',
        'preview_color' => '#6B3FBF',
        'is_active'     => true,
        'sort_order'    => 0,
        'planet_number' => 1,
    ];

    protected array $rules = [
        'form.name'          => 'required|string|max:100',
        'form.description'   => 'nullable|string|max:255',
        'form.preview_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        'form.is_active'     => 'boolean',
        'form.sort_order'    => 'integer|min:0',
        'form.planet_number' => 'required|integer|min:1|max:21',
    ];

    protected array $messages = [
        'form.name.required'             => 'กรุณาระบุชื่อ category',
        'form.preview_color.regex'       => 'กรุณาระบุสีในรูปแบบ hex (#RRGGBB)',
        'form.planet_number.required'    => 'กรุณาเลือกดาวเคราะห์',
        'form.planet_number.min'         => 'หมายเลขดาวเคราะห์ต้องอยู่ระหว่าง 1-21',
        'form.planet_number.max'         => 'หมายเลขดาวเคราะห์ต้องอยู่ระหว่าง 1-21',
    ];
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset('form', 'editingId');
        $this->form = [
            'name'          => '',
            'description'   => '',
            'preview_color' => '#6B3FBF',
            'is_active'     => true,
            'sort_order'    => 0,
            'planet_number' => 1,
        ];
        $this->showModal = true;
    }

    public function openEdit(ClassroomThemeCategory $category): void
    {
        $this->editingId = $category->id;
        $this->form = [
            'name'          => $category->name,
            'description'   => $category->description ?? '',
            'preview_color' => $category->preview_color,
            'is_active'     => $category->is_active,
            'sort_order'    => $category->sort_order,
            'planet_number' => $category->planet_number,
        ];
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = $this->form;

        if ($this->editingId) {
            $category = ClassroomThemeCategory::findOrFail($this->editingId);
            $category->update($data);
            session()->flash('message', __('อัปเดต category สำเร็จ'));
        } else {
            ClassroomThemeCategory::create($data);
            session()->flash('message', __('สร้าง category สำเร็จ'));
        }

        $this->showModal = false;
        $this->reset('form', 'editingId');
    }

    public function delete(ClassroomThemeCategory $category): void
    {
        $category->delete();
        session()->flash('message', __('ลบ category สำเร็จ'));
    }

    public function render()
    {
        $categories = ClassroomThemeCategory::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(7);

        return view('livewire.admin.theme-categories', ['categories' => $categories]);
    }
}
