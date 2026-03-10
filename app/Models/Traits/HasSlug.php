<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Return the slug for URL generation.
     * Falls back to id if slug is not yet set (legacy records).
     */
    public function getRouteKey(): string|int
    {
        return $this->slug ?? $this->getKey();
    }

    /**
     * Resolve route binding by slug first, then by id (legacy fallback).
     * When found by id, backfill the slug automatically.
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $field = $field ?? $this->getRouteKeyName();

        $model = $this->where($field, $value)->first();

        // Fallback: try by primary key for legacy records without slugs
        if (! $model && is_numeric($value)) {
            $model = $this->where($this->getKeyName(), $value)->first();

            // Backfill slug for legacy record
            if ($model && empty($model->slug)) {
                $model->slug = static::generateUniqueSlug();
                $model->saveQuietly();
            }
        }

        return $model;
    }

    public static function bootHasSlug(): void
    {
        static::creating(function ($model): void {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug();
            }
        });
    }

    public static function generateUniqueSlug(): string
    {
        do {
            $slug = strtolower(Str::random(16));
        } while (static::where('slug', $slug)->exists());

        return $slug;
    }
}
