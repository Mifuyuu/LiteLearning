<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug();
            }
        });
    }

    public static function generateUniqueSlug(): string
    {
        do {
            $slug = Str::random(16);
        } while (static::where('slug', $slug)->exists());

        return $slug;
    }
}
