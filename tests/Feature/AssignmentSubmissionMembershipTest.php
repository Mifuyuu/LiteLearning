<?php

namespace Tests\Feature;

use App\Livewire\Assignment\Grade as AssignmentGrade;
use App\Livewire\Assignment\Show as AssignmentShow;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssignmentSubmissionMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_turn_in_after_leaving_classroom_mid_session(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $assignment = Assignment::factory()->create([
            'user_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'type' => 'file',
            'status' => 'published',
        ]);

        $component = Livewire::actingAs($student)
            ->test(AssignmentShow::class, ['classroom' => $classroom, 'assignment' => $assignment]);

        // Student leaves the classroom while this page instance is still open (stale/bfcache page).
        $classroom->members()->detach($student->id);

        $component->call('turnIn')->assertForbidden();
    }

    public function test_teacher_does_not_see_submission_row_for_a_student_who_left(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $assignment = Assignment::factory()->create([
            'user_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'type' => 'file',
            'status' => 'published',
        ]);

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'status' => 'turned_in',
            'turned_in_at' => now(),
        ]);

        $classroom->members()->detach($student->id);

        Livewire::actingAs($teacher)
            ->test(AssignmentShow::class, ['classroom' => $classroom, 'assignment' => $assignment])
            ->assertDontSee($student->name)
            ->assertDontSee(route('assignment.grade', ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $submission]));
    }

    public function test_teacher_sees_submission_row_again_after_student_rejoins(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $assignment = Assignment::factory()->create([
            'user_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'type' => 'file',
            'status' => 'published',
        ]);

        Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'status' => 'turned_in',
            'turned_in_at' => now(),
        ]);

        $classroom->members()->detach($student->id);

        Livewire::actingAs($teacher)
            ->test(AssignmentShow::class, ['classroom' => $classroom, 'assignment' => $assignment])
            ->assertDontSee($student->name);

        // Student rejoins the same classroom — their old submission reappears.
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        Livewire::actingAs($teacher)
            ->test(AssignmentShow::class, ['classroom' => $classroom, 'assignment' => $assignment])
            ->assertSee($student->name);
    }

    public function test_teacher_cannot_open_grade_page_for_a_submission_from_a_student_who_left(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $assignment = Assignment::factory()->create([
            'user_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'type' => 'file',
            'status' => 'published',
        ]);

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'status' => 'turned_in',
            'turned_in_at' => now(),
        ]);

        $classroom->members()->detach($student->id);

        Livewire::actingAs($teacher)
            ->test(AssignmentGrade::class, ['classroom' => $classroom, 'assignment' => $assignment, 'submission' => $submission])
            ->assertStatus(404);
    }
}
