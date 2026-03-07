<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'name',
        'order',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function assignments(): Builder
    {
        return Assignment::query()
            ->where('classroom_id', $this->classroom_id)
            ->where('topic', $this->name);
    }
}
