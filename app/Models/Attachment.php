<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

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
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }

    public function getIconAttribute(): string
    {
        $type = $this->file_type;

        if (str_contains($type, 'image')) {
            return 'photo';
        }
        if (str_contains($type, 'video')) {
            return 'play-circle';
        }
        if (str_contains($type, 'zip') || str_contains($type, 'rar')) {
            return 'archive-box';
        }
        if (
            str_contains($type, 'pdf') ||
            str_contains($type, 'word') ||
            str_contains($type, 'excel') ||
            str_contains($type, 'spreadsheet') ||
            str_contains($type, 'powerpoint') ||
            str_contains($type, 'presentation')
        ) {
            return 'document-text';
        }

        return 'document';
    }
}
