<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\ClassworkItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    private const PARENT_KEYS = ['user_id', 'classroom_id', 'title', 'description', 'topic_id', 'slug'];

    public function definition(): array
    {
        return [
            'classwork_item_id' => ClassworkItem::factory()->forAssignment(),
            'max_score' => fake()->randomElement([10, 20, 50, 100]),
            'due_date' => fake()->dateTimeBetween('now', '+2 weeks'),
            'status' => 'published',
            'type' => 'file',
            'allow_late_submission' => true,
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
                : ClassworkItem::factory()->forAssignment()->state($parentAttrs);
        }

        return parent::expandAttributes($childAttrs);
    }

    public function announcement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'announcement',
            'max_score' => 0,
        ]);
    }

    public function attendance(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'attendance',
            'max_score' => 1,
        ]);
    }

    public function file(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'file',
        ]);
    }

    public function topic(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'topic',
            'max_score' => 0,
        ]);
    }

    public function material(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'material',
            'max_score' => 0,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function noLateSubmission(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_late_submission' => false,
        ]);
    }
}
