<?php

namespace App\Livewire\Concerns;

trait HasEditableContent
{
    public bool $isEditTab = false;

    public bool $showDeleteModal = false;

    /**
     * Return a map of edit-property names to model attribute names.
     * Example: ['editTitle' => 'title', 'editDescription' => 'description']
     */
    abstract protected function editableFields(): array;

    /**
     * Return the Eloquent model instance being edited.
     */
    abstract protected function getEditableModel(): \Illuminate\Database\Eloquent\Model;

    public function openEditTab(): void
    {
        $this->isEditTab = true;
        $this->syncEditFields();
    }

    public function cancelEditTab(): void
    {
        $this->isEditTab = false;
    }

    protected function syncEditFields(): void
    {
        $model = $this->getEditableModel();

        foreach ($this->editableFields() as $property => $attribute) {
            if ($attribute === 'due_date') {
                $this->{$property} = $model->{$attribute}?->format('Y-m-d\TH:i');
            } else {
                $this->{$property} = $model->{$attribute} ?? (is_bool($this->{$property}) ? false : '');
            }
        }
    }

    public function openDeleteModal(): void
    {
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
    }
}
