<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassroomIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_without_classrooms_sees_centered_empty_state(): void
    {
        /** @var User $student */
        $student = User::factory()->createOne(['role' => 'student']);

        $this->actingAs($student)
            ->get(route('classrooms'))
            ->assertOk()
            ->assertSee('data-classroom-empty-state', false)
            ->assertSee('data-empty-state-layout="remaining-content"', false)
            ->assertSee('data-empty-state-centered="true"', false)
            ->assertSee('data-empty-state-image-crop', false)
            ->assertSee('/images/empty.svg', false)
            ->assertSeeText('ยังไม่มีดวงดาวที่ค้นพบ...');
    }

    public function test_teacher_without_classrooms_sees_centered_empty_state(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->createOne(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('classrooms'))
            ->assertOk()
            ->assertSee('data-classroom-empty-state', false)
            ->assertSee('data-empty-state-layout="remaining-content"', false)
            ->assertSee('data-empty-state-centered="true"', false)
            ->assertSee('data-empty-state-image-crop', false)
            ->assertSee('/images/empty.svg', false)
            ->assertSeeText('ยังไม่มีดวงดาวที่ค้นพบ...');
    }

    public function test_student_classrooms_page_shows_only_active_enrolled_classrooms_without_filter_controls(): void
    {
        /** @var User $student */
        $student = User::factory()->createOne(['role' => 'student']);
        /** @var User $teacher */
        $teacher = User::factory()->createOne(['role' => 'teacher']);

        $activeClassroom = Classroom::factory()->create([
            'teacher_id' => $teacher->id,
            'name' => 'Active Astronomy',
        ]);

        $archivedClassroom = Classroom::factory()->archived()->create([
            'teacher_id' => $teacher->id,
            'name' => 'Archived Biology',
        ]);

        $activeClassroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);
        $archivedClassroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $response = $this->actingAs($student)->get(route('classrooms'));

        $response->assertOk();
        $response->assertSeeText('Active Astronomy');
        $response->assertDontSeeText('Archived Biology');
        $response->assertDontSeeText('ทั้งหมด');
        $response->assertDontSee('wire:click="$set(\'filter\',\'enrolled\')"', false);
        $response->assertDontSee('wire:click="$set(\'filter\',\'archived\')"', false);
    }

    public function test_teacher_classrooms_page_includes_co_teaching_classrooms(): void
    {
        /** @var User $owner */
        $owner = User::factory()->createOne(['role' => 'teacher']);
        /** @var User $coTeacher */
        $coTeacher = User::factory()->createOne(['role' => 'teacher']);

        $coTeachingClassroom = Classroom::factory()->create([
            'teacher_id' => $owner->id,
            'name' => 'Shared Physics',
        ]);

        $coTeachingClassroom->members()->attach($coTeacher->id, [
            'role' => 'co-teacher',
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($coTeacher)->get(route('classrooms'));

        $response->assertOk();
        $response->assertSeeText('Shared Physics');
    }
}
