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
        $this->validate([
            'file' => 'file|max:'.$this->maxFileSizeKb().'|mimes:'.$this->allowedMimes(),
        ]);

        if ($this->file) {
            $this->uploadedFiles[] = [
                'tmpPath' => $this->file->getRealPath(),
                'name' => $this->file->getClientOriginalName(),
                'size' => $this->file->getSize(),
                'mime' => $this->file->getMimeType(),
                'file' => $this->file,
                'id' => $this->generateAttachmentId(),
            ];
            $this->file = null;
        }
    }

    public function removeFile(int $index): void
    {
        unset($this->uploadedFiles[$index]);
        $this->uploadedFiles = array_values($this->uploadedFiles);
    }

    protected function generateAttachmentId(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $id = '';
        for ($i = 0; $i < 8; $i++) {
            $id .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $id;
    }
}
