<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ClassroomThemeCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'preview_color',
        'is_active',
        'sort_order',
        'planet_number',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'planet_number' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ClassroomThemeCategory $category) {
            if (empty($category->slug)) {
                $base = 'planet-' . $category->planet_number;
                $category->slug = $base;
            }
        });
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
