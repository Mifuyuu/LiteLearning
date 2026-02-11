<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'classroom_id' => Classroom::factory(),
            'user_id' => User::factory(),
            'content' => fake()->paragraphs(rand(1, 3), true),
        ];
    }
}
