<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('s3')->url($this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    public function getIconAttribute(): string
    {
        return match (true) {
            str_contains($this->file_type, 'image') => 'fa-file-image',
            str_contains($this->file_type, 'pdf') => 'fa-file-pdf',
            str_contains($this->file_type, 'word') => 'fa-file-word',
            str_contains($this->file_type, 'excel'), str_contains($this->file_type, 'spreadsheet') => 'fa-file-excel',
            str_contains($this->file_type, 'powerpoint'), str_contains($this->file_type, 'presentation') => 'fa-file-powerpoint',
            str_contains($this->file_type, 'video') => 'fa-file-video',
            str_contains($this->file_type, 'audio') => 'fa-file-audio',
            str_contains($this->file_type, 'zip'), str_contains($this->file_type, 'rar') => 'fa-file-archive',
            default => 'fa-file',
        };
    }
}
