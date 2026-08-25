<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\ClassworkItem;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\AssignmentDueSoon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DueDateReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_reminder_only_once_for_assignment_due_within_24_hours(): void
    {
        Notification::fake();

        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);

        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $item = ClassworkItem::create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'type' => 'assignment',
            'title' => 'การบ้านดาราศาสตร์',
            'published_at' => now()->subHour(),
        ]);

        $assignment = Assignment::create([
            'classwork_item_id' => $item->id,
            'status' => 'published',
            'type' => 'homework',
            'due_date' => now()->addHours(20),
        ]);

        // First run: should send notification
        $this->artisan('assignment:send-due-reminders')
            ->assertSuccessful();

        Notification::assertSentTo($student, AssignmentDueSoon::class, 1);

        // Second run: should skip due to cache deduplication (not send again)
        $this->artisan('assignment:send-due-reminders')
            ->assertSuccessful();

        Notification::assertSentTo($student, AssignmentDueSoon::class, 1);
    }

    public function test_does_not_send_reminder_if_already_turned_in(): void
    {
        Notification::fake();

        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);

        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $item = ClassworkItem::create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'type' => 'assignment',
            'title' => 'การบ้านฟิสิกส์',
            'published_at' => now()->subHour(),
        ]);

        $assignment = Assignment::create([
            'classwork_item_id' => $item->id,
            'status' => 'published',
            'type' => 'homework',
            'due_date' => now()->addHours(15),
        ]);

        Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'status' => 'turned_in',
            'submitted_at' => now(),
        ]);

        $this->artisan('assignment:send-due-reminders')
            ->assertSuccessful();

        Notification::assertNothingSent();
    }
}
