<?php

namespace App\Livewire\Concerns;

trait HasFileUpload
{
    public $file = null;

    public array $uploadedFiles = [];

    /**
     * Return allowed mime types as a comma-separated string for validation.
     */
    abstract protected function allowedMimes(): string;

    /**
     * Return the max file size in KB.
     */
    abstract protected function maxFileSizeKb(): int;

    public function updatedFile(): void
    {
        $files = is_array($this->file) ? $this->file : array_filter([$this->file]);

        if (! $files) {
            return;
        }

        $this->validate([
            'file.*' => 'file|max:'.$this->maxFileSizeKb().'|mimes:'.$this->allowedMimes(),
        ]);

        foreach ($files as $file) {
            $this->uploadedFiles[] = [
                'tmpPath' => $file->getRealPath(),
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'file' => $file,
                'id' => $this->generateAttachmentId(),
            ];
        }

        $this->file = null;
    }

    public function removeFile(int $index): void
    {
        unset($this->uploadedFiles[$index]);
        $this->uploadedFiles = array_values($this->uploadedFiles);
    }

    protected function generateAttachmentId(): string
    {
        return \Illuminate\Support\Str::random(8);
    }
}
