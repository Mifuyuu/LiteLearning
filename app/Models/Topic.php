<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    public function classworkItems(): HasMany
    {
        return $this->hasMany(ClassworkItem::class);
    }

    public function assignments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Assignment::class,
            ClassworkItem::class,
            'topic_id',
            'classwork_item_id',
            'id',
            'id'
        );
    }
}
