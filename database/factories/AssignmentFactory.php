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
            'max_score' => fake()->randomElement([10, 20, 50, 100]),
            'due_date' => fake()->dateTimeBetween('now', '+2 weeks'),
            'status' => 'published',
            'type' => 'question',
            'allow_late_submission' => true,
        ];
    }

    public function attendance(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'attendance',
            'max_score' => 1,
        ]);
    }

    public function file(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'file',
        ]);
    }

    public function question(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'question',
        ]);
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

    public function noLateSubmission(): static
    {
        return $this->state(fn(array $attributes) => [
            'allow_late_submission' => false,
        ]);
    }
}
