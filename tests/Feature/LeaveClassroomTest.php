<?php

namespace Tests\Feature;

use App\Livewire\Classroom\LeaveClassroom;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeaveClassroomTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_leave_a_classroom_they_are_a_member_of(): void
    {
        /** @var User $student */
        $student = User::factory()->createOne(['role' => 'student']);
        $classroom = Classroom::factory()->create();
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $this->actingAs($student);

        Livewire::test(LeaveClassroom::class)
            ->call('openModal', $classroom->id)
            ->call('leave')
            ->assertRedirect(route('classrooms'));

        $this->assertFalse($classroom->fresh()->hasMember($student->fresh()));
    }

    public function test_student_cannot_leave_a_classroom_they_do_not_belong_to(): void
    {
        /** @var User $student */
        $student = User::factory()->createOne(['role' => 'student']);
        $classroom = Classroom::factory()->create();

        $this->actingAs($student);

        Livewire::test(LeaveClassroom::class)
            ->call('openModal', $classroom->id)
            ->call('leave')
            ->assertForbidden();
    }

    public function test_teacher_cannot_leave_their_own_classroom(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->createOne(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher);

        Livewire::test(LeaveClassroom::class)
            ->call('openModal', $classroom->id)
            ->call('leave')
            ->assertForbidden();
    }
}
