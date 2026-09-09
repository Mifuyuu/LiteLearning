<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Regression test for: submitted-count badge on the teacher side kept
// counting a submission from a student who had already left the classroom,
// so the "X/Y ส่งแล้ว" badge never went down after a student left.
class SubmittedCountExcludesLeftStudentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitted_count_excludes_students_who_left_the_classroom(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $stayingStudent = User::factory()->create(['role' => 'student']);
        $leavingStudent = User::factory()->create(['role' => 'student']);

        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($stayingStudent->id, ['role' => 'student', 'joined_at' => now()]);
        $classroom->members()->attach($leavingStudent->id, ['role' => 'student', 'joined_at' => now()]);

        $assignment = Assignment::factory()->file()->create([
            'user_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'status' => 'published',
        ]);

        Submission::create(['assignment_id' => $assignment->id, 'user_id' => $stayingStudent->id, 'status' => 'turned_in', 'turned_in_at' => now()]);
        Submission::create(['assignment_id' => $assignment->id, 'user_id' => $leavingStudent->id, 'status' => 'turned_in', 'turned_in_at' => now()]);

        $classroom->refresh();
        $this->assertSame(2, $assignment->submittedCount($classroom->students->pluck('id')));

        // Student leaves the classroom; their submission row still exists.
        $classroom->members()->detach($leavingStudent->id);
        $classroom->load('students');

        $this->assertSame(1, $assignment->submittedCount($classroom->students->pluck('id')));

        // Without the classroom filter, the stale total is still visible (for
        // historical/legacy call sites), which is why the filter is required.
        $this->assertSame(2, $assignment->submittedCount());
    }

    public function test_work_item_card_badge_reflects_departure(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $stayingStudent = User::factory()->create(['role' => 'student']);
        $leavingStudent = User::factory()->create(['role' => 'student']);

        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($stayingStudent->id, ['role' => 'student', 'joined_at' => now()]);
        $classroom->members()->attach($leavingStudent->id, ['role' => 'student', 'joined_at' => now()]);

        $assignment = Assignment::factory()->file()->create([
            'user_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'status' => 'published',
            'title' => 'Homework With Departures',
        ]);

        Submission::create(['assignment_id' => $assignment->id, 'user_id' => $stayingStudent->id, 'status' => 'turned_in', 'turned_in_at' => now()]);
        Submission::create(['assignment_id' => $assignment->id, 'user_id' => $leavingStudent->id, 'status' => 'turned_in', 'turned_in_at' => now()]);

        $classroom->members()->detach($leavingStudent->id);

        $this->actingAs($teacher)
            ->get(route('classroom.work', ['classroom' => $classroom->slug]))
            ->assertOk()
            ->assertSee('1/1');
    }
}
