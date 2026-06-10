<?php

namespace Tests\Feature;

use App\Livewire\Classroom\Stream as ClassroomStream;
use App\Livewire\Classroom\Work as ClassroomWork;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClassroomContentMigrationSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_sees_only_current_classroom_announcements_and_assignments(): void
    {
        $teacher = User::factory()->createOne(['role' => 'teacher']);
        $student = User::factory()->createOne(['role' => 'student']);
        assert($teacher instanceof User);
        assert($student instanceof User);

        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $otherClassroom = Classroom::factory()->create();

        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $announcement = Announcement::factory()->create([
            'user_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'content' => 'Announcement visible in this classroom',
        ]);

        Announcement::factory()->create([
            'classroom_id' => $otherClassroom->id,
            'content' => 'Announcement hidden from another classroom',
        ]);

        $publishedAssignment = Assignment::factory()->question()->create([
            'user_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'title' => 'Published assignment visible to students',
            'status' => 'published',
        ]);

        Assignment::factory()->question()->create([
            'classroom_id' => $otherClassroom->id,
            'title' => 'Assignment hidden from another classroom',
            'status' => 'published',
        ]);

        Livewire::actingAs($student)
            ->test(ClassroomStream::class, ['classroom' => $classroom])
            ->assertSee($announcement->content)
            ->assertDontSee('Announcement hidden from another classroom');

        Livewire::actingAs($student)
            ->test(ClassroomWork::class, ['classroom' => $classroom])
            ->assertSee($publishedAssignment->title)
            ->assertDontSee('Assignment hidden from another classroom');
    }

    public function test_assignment_route_rejects_assignment_from_different_classroom_before_submission_flow(): void
    {
        $teacher = User::factory()->createOne(['role' => 'teacher']);
        assert($teacher instanceof User);

        $classroomA = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroomB = Classroom::factory()->create();
        $assignment = Assignment::factory()->create(['classroom_id' => $classroomB->id]);

        $this->actingAs($teacher);

        $response = $this->get(route('assignment.show', [
            'classroom' => $classroomA->slug,
            'assignment' => $assignment->slug,
        ]));

        $response->assertStatus(404);
    }

    public function test_attendance_assignment_route_rejects_assignment_from_different_classroom(): void
    {
        $teacher = User::factory()->createOne(['role' => 'teacher']);
        assert($teacher instanceof User);

        $classroomA = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroomB = Classroom::factory()->create();
        $attendance = Assignment::factory()->attendance()->create(['classroom_id' => $classroomB->id]);

        $this->actingAs($teacher);

        $response = $this->get(route('assignment.show', [
            'classroom' => $classroomA->slug,
            'assignment' => $attendance->slug,
        ]));

        $response->assertStatus(404);
    }
}
