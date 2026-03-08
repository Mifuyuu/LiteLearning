<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThemeCategory extends Model
{
    protected $fillable = [
        'name',
        'color',
        'is_active',

        'planet_number',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',

            'planet_number' => 'integer',
        ];
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class, 'theme_category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
