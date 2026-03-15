<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::generateSlugForModel($model);
            }
        });
    }

    public static function generateUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug ?: Str::random(8);
        $counter = 2;

        while (\App\Models\ClassworkItem::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private static function generateSlugForModel(object $model): string
    {
        $base = isset($model->title) && $model->title
            ? Str::slug($model->title)
            : Str::random(8);
        $slug = $base ?: Str::random(8);
        $counter = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
