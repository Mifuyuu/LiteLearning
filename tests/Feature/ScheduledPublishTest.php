<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\ClassworkItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduledPublishTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduled_assignment_is_published_at_scheduled_time(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $classworkItem = ClassworkItem::create([
            'type' => 'assignment',
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Test Assignment',
            'slug' => 'test-assignment-sched',
            'description' => null,
            'published_at' => Carbon::now()->subMinute(),
        ]);

        $assignment = Assignment::create([
            'classwork_item_id' => $classworkItem->id,
            'max_score' => 100,
            'exp_reward' => 0,
            'coin_reward' => 0,
            'due_date' => null,
            'status' => 'scheduled',
            'type' => 'question',
            'allow_late_submission' => true,
        ]);

        $this->artisan('classwork:publish-scheduled')->assertExitCode(0);

        $assignment->refresh();
        $this->assertEquals('published', $assignment->status);
        $this->assertCount(1, $assignment->submissions);
        $this->assertEquals($student->id, $assignment->submissions->first()->user_id);
    }

    public function test_future_scheduled_assignment_is_not_published_yet(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);

        $classworkItem = ClassworkItem::create([
            'type' => 'assignment',
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Future Assignment',
            'slug' => 'future-assignment-sched',
            'description' => null,
            'published_at' => Carbon::now()->addHour(),
        ]);

        Assignment::create([
            'classwork_item_id' => $classworkItem->id,
            'max_score' => 100,
            'exp_reward' => 0,
            'coin_reward' => 0,
            'due_date' => null,
            'status' => 'scheduled',
            'type' => 'question',
            'allow_late_submission' => true,
        ]);

        $this->artisan('classwork:publish-scheduled')->assertExitCode(0);

        $this->assertDatabaseHas('assignments', [
            'classwork_item_id' => $classworkItem->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_duplicate_submissions_not_created(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $classworkItem = ClassworkItem::create([
            'type' => 'assignment',
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Dup Test',
            'slug' => 'dup-test-sched',
            'description' => null,
            'published_at' => Carbon::now()->subMinute(),
        ]);

        $assignment = Assignment::create([
            'classwork_item_id' => $classworkItem->id,
            'max_score' => 100,
            'exp_reward' => 0,
            'coin_reward' => 0,
            'due_date' => null,
            'status' => 'scheduled',
            'type' => 'question',
            'allow_late_submission' => true,
        ]);

        // Pre-existing submission
        $assignment->submissions()->create(['user_id' => $student->id, 'status' => 'assigned']);

        $this->artisan('classwork:publish-scheduled')->assertExitCode(0);

        $this->assertCount(1, $assignment->fresh()->submissions);
    }
}
