<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\ClassworkItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassworkItemFactory extends Factory
{
    protected $model = ClassworkItem::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'type' => 'assignment',
            'classroom_id' => Classroom::factory(),
            'user_id' => User::factory(),
            'topic_id' => null,
            'title' => $title,
            'slug' => \App\Models\ClassworkItem::generateUniqueSlug(),
            'description' => fake()->paragraph(),
        ];
    }

    public function forAssignment(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'assignment',
        ]);
    }

    public function forMaterial(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'material',
        ]);
    }

    public function forAnnouncement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'announcement',
        ]);
    }

    public function forAttendance(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'attendance',
        ]);
    }
}
