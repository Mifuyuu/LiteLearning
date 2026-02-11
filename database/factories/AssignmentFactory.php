<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        return [
            'classroom_id' => Classroom::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'instructions' => fake()->paragraphs(2, true),
            'max_score' => fake()->randomElement([10, 20, 50, 100]),
            'due_date' => fake()->dateTimeBetween('now', '+2 weeks'),
            'status' => 'published',
            'type' => 'assignment',
        ];
    }

    public function quiz(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'quiz',
        ]);
    }

    public function material(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'material',
            'max_score' => 0,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'draft',
        ]);
    }
}
