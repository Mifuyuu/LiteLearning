<?php

namespace App\Livewire\Admin;

use App\Models\StoreItem;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Store extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $editingId = null;

    // Form fields
    public $form = [
        'code' => '',
        'name' => '',
        'description' => '',
        'type' => 'name_color',
        'value' => '',
        'price' => 100,
        'is_active' => true,
    ];

    protected $rules = [
        'form.code' => 'required|string|max:100',
        'form.name' => 'required|string|max:255',
        'form.description' => 'nullable|string',
        'form.type' => 'required|in:name_color,avatar_frame',
        'form.value' => 'required|string|max:255',
        'form.price' => 'required|integer|min:0',
        'form.is_active' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->validateOnly('search', ['search' => 'string|max:100']);
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->editingId = null;
        $this->form = ['code' => '', 'name' => '', 'description' => '', 'type' => 'name_color', 'value' => '', 'price' => 100, 'is_active' => true];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(StoreItem $item)
    {
        $this->editingId = $item->id;
        $this->form = [
            'code' => $item->code,
            'name' => $item->name,
            'description' => $item->description,
            'type' => $item->type,
            'value' => $item->value,
            'price' => $item->price,
            'is_active' => $item->is_active,
        ];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save()
    {
        // S1: validate code uniqueness (ignore current item when editing)
        $uniqueRule = 'unique:store_items,code';
        if ($this->editingId) {
            $uniqueRule .= ','.$this->editingId;
        }
        $this->rules['form.code'] = 'required|string|max:100|'.$uniqueRule;

        $this->validate();

        if ($this->editingId) {
            $item = StoreItem::findOrFail($this->editingId);
            $item->update($this->form);
            $this->dispatch('notify', message: 'อัปเดตสินค้าแล้ว');
        } else {
            StoreItem::create($this->form);
            $this->dispatch('notify', message: 'เพิ่มสินค้าแล้ว');
        }

        $this->showModal = false;
        $this->resetValidation();
    }

    public function toggleActive(StoreItem $item)
    {
        $item->is_active = ! $item->is_active;
        $item->save();

        $this->dispatch('notify', message: 'อัปเดตสถานะสินค้าแล้ว');
    }

    public function delete(StoreItem $item)
    {
        $item->delete();
        $this->dispatch('notify', message: 'ลบสินค้าแล้ว');
    }

    public function render()
    {
        $query = StoreItem::query();

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('code', 'like', '%'.$this->search.'%');
        }

        return view('livewire.admin.store', [
            'items' => $query->latest()->paginate(10),
        ]);
    }
}
