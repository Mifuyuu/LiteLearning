<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\ClassworkItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    private const PARENT_KEYS = ['user_id', 'classroom_id', 'title', 'slug'];

    public function definition(): array
    {
        return [
            'classwork_item_id' => ClassworkItem::factory()->forAnnouncement(),
            'content' => fake()->paragraphs(rand(1, 3), true),
        ];
    }

    protected function expandAttributes(array $definition): array
    {
        [$parentAttrs, $childAttrs] = [[], []];

        foreach ($definition as $key => $value) {
            if (in_array($key, self::PARENT_KEYS, true)) {
                $parentAttrs[$key] = $value;
            } else {
                $childAttrs[$key] = $value;
            }
        }

        if (! empty($parentAttrs)) {
            $existing = $childAttrs['classwork_item_id'] ?? null;

            $childAttrs['classwork_item_id'] = $existing instanceof \Illuminate\Database\Eloquent\Factories\Factory
                ? $existing->state($parentAttrs)
                : ClassworkItem::factory()->forAnnouncement()->state($parentAttrs);
        }

        return parent::expandAttributes($childAttrs);
    }
}
