<?php

namespace App\Livewire\Admin;

use App\Models\StoreItem;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Store extends Component
{
    use WithFileUploads, WithPagination;

    public $search = '';

    public $showModal = false;

    public $editingId = null;

    public mixed $frameImageUpload = null;

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
        'frameImageUpload' => 'nullable|file|mimes:png,svg,webp|max:2048',
    ];

    protected function messages(): array
    {
        return [
            'form.code.required' => __('messages.validation.code_store'),
            'form.name.required' => __('messages.validation.name_store_item'),
            'form.type.required' => __('messages.validation.type_store'),
            'form.value.required' => __('messages.validation.value_store'),
            'form.price.required' => __('messages.validation.price'),
        ];
    }

    public function updatingSearch()
    {
        $this->validateOnly('search', ['search' => 'string|max:100']);
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->editingId = null;
        $this->frameImageUpload = null;
        $this->form = ['code' => '', 'name' => '', 'description' => '', 'type' => 'name_color', 'value' => '', 'price' => 100, 'is_active' => true];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(StoreItem $item)
    {
        $this->editingId = $item->id;
        $this->frameImageUpload = null;
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

        // Value is filled from the uploaded frame image after validation, so it's not required up front for that case.
        $this->rules['form.value'] = ($this->form['type'] === 'avatar_frame' && $this->frameImageUpload)
            ? 'nullable|string|max:255'
            : 'required|string|max:255';

        $this->validate();

        if ($this->frameImageUpload) {
            $filename = $this->frameImageUpload->hashName();
            $this->frameImageUpload->storeAs('', $filename, ['disk' => 'frames']);
            $this->form['value'] = 'images/frames/'.$filename;
        }

        if ($this->editingId) {
            $item = StoreItem::findOrFail($this->editingId);
            $item->update($this->form);
            $this->dispatch('notify', message: __('messages.admin.store_updated'));
        } else {
            StoreItem::create($this->form);
            $this->dispatch('notify', message: __('messages.admin.store_created'));
        }

        $this->showModal = false;
        $this->frameImageUpload = null;
        $this->resetValidation();
    }

    public function toggleActive(StoreItem $item)
    {
        $item->is_active = ! $item->is_active;
        $item->save();

        $this->dispatch('notify', message: __('messages.admin.store_status_updated'));
    }

    public function delete(StoreItem $item)
    {
        $item->delete();
        $this->dispatch('notify', message: __('messages.admin.store_deleted'));
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
