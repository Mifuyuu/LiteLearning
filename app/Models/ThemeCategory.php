<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThemeCategory extends Model
{
    // Used when a classroom has no theme assigned, so the fallback planet image and
    // fallback color always match (kept in sync with ThemeCategorySeeder).
    public const FALLBACK_THEMES = [
        ['planet_key' => 'almond', 'color' => '#B85623'],
        ['planet_key' => 'cheese', 'color' => '#FFD663'],
        ['planet_key' => 'chocolate', 'color' => '#C8774B'],
        ['planet_key' => 'coffee', 'color' => '#C2793B'],
        ['planet_key' => 'darkhole', 'color' => '#9D4CFF'],
        ['planet_key' => 'earth', 'color' => '#00A4D2'],
        ['planet_key' => 'emerald', 'color' => '#60D79E'],
        ['planet_key' => 'evergreen', 'color' => '#89DA2B'],
        ['planet_key' => 'krypton', 'color' => '#F3414E'],
        ['planet_key' => 'lovely', 'color' => '#DE3F76'],
        ['planet_key' => 'neptune', 'color' => '#3CBEBE'],
        ['planet_key' => 'pluto', 'color' => '#E4E4E4'],
        ['planet_key' => 'ruby', 'color' => '#F15D75'],
        ['planet_key' => 'saturn', 'color' => '#E08A3C'],
        ['planet_key' => 'sun', 'color' => '#FC8827'],
        ['planet_key' => 'uranus', 'color' => '#7E91A8'],
        ['planet_key' => 'virus', 'color' => '#1D848C'],
        ['planet_key' => 'void', 'color' => '#E0A6FE'],
        ['planet_key' => 'waterfall', 'color' => '#6F74C6'],
        ['planet_key' => 'whitehole', 'color' => '#484848'],
    ];

    public static function fallbackFor(int $seed): array
    {
        return self::FALLBACK_THEMES[$seed % count(self::FALLBACK_THEMES)];
    }

    protected $fillable = [
        'name',
        'color',
        'is_active',

        'planet_key',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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
