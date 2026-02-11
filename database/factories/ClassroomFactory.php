<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        $subjects = ['Mathematics', 'Science', 'English', 'History', 'Computer Science', 'Physics', 'Chemistry', 'Biology', 'Art', 'Music'];
        $sections = ['Section A', 'Section B', 'Section C', 'Period 1', 'Period 2', 'Period 3'];
        $colors = ['#4F46E5', '#059669', '#DC2626', '#D97706', '#7C3AED', '#2563EB', '#DB2777'];

        return [
            'teacher_id' => User::factory(),
            'name' => fake()->randomElement($subjects) . ' ' . fake()->numberBetween(101, 499),
            'section' => fake()->randomElement($sections),
            'subject' => fake()->randomElement($subjects),
            'description' => fake()->paragraph(),
            'theme_color' => fake()->randomElement($colors),
            'is_archived' => false,
        ];
    }

    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_archived' => true,
        ]);
    }
}
