<?php

namespace Tests\Feature;

use App\Livewire\Assignment\Create;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

// Renders every branch touched by the assignment-show / classroom-work
// layout merge so a Blade mistake (bad @if/@endif nesting, a stray closing
// div) surfaces as a hard render failure instead of shipping silently.
class AssignmentShowRenderTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    private User $student;

    private Classroom $classroom;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->student = User::factory()->create(['role' => 'student']);

        $this->classroom = Classroom::factory()->create(['teacher_id' => $this->teacher->id]);
        $this->classroom->members()->attach($this->student->id, ['role' => 'student', 'joined_at' => now()]);
    }

    private function showUrl(Assignment $assignment): string
    {
        return route('assignment.show', [
            'classroom' => $this->classroom->slug,
            'assignment' => $assignment->slug,
        ]);
    }

    public function test_file_assignment_show_renders_for_teacher_with_submissions_and_stats(): void
    {
        $assignment = Assignment::factory()->file()->create([
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'status' => 'published',
        ]);
        Submission::create(['assignment_id' => $assignment->id, 'user_id' => $this->student->id, 'status' => 'turned_in', 'turned_in_at' => now()]);

        $this->actingAs($this->teacher)
            ->get($this->showUrl($assignment))
            ->assertOk()
            ->assertSee('งานนักเรียน') // submissions table section
            ->assertSee('ข้อมูลงาน'); // stats tiles section
    }

    public function test_file_assignment_show_renders_for_student_with_submission_box(): void
    {
        $assignment = Assignment::factory()->file()->create([
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'status' => 'published',
        ]);
        Submission::create(['assignment_id' => $assignment->id, 'user_id' => $this->student->id, 'status' => 'assigned']);

        $this->actingAs($this->student)
            ->get($this->showUrl($assignment))
            ->assertOk()
            ->assertSee('งานของคุณ');
    }

    public function test_attendance_assignment_show_renders_for_teacher(): void
    {
        $assignment = Assignment::factory()->attendance()->create([
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'status' => 'published',
        ]);

        $this->actingAs($this->teacher)
            ->get($this->showUrl($assignment))
            ->assertOk()
            ->assertSee('เซสชันเช็คชื่อ');
    }

    public function test_attendance_assignment_show_renders_for_student(): void
    {
        $assignment = Assignment::factory()->attendance()->create([
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'status' => 'published',
        ]);
        Submission::create(['assignment_id' => $assignment->id, 'user_id' => $this->student->id, 'status' => 'assigned']);

        $this->actingAs($this->student)
            ->get($this->showUrl($assignment))
            ->assertOk()
            ->assertDontSee('งานของคุณ') // attendance has its own check-in box, not the file/question submission box
            ->assertSee('เช็คชื่อ');
    }

    public function test_material_assignment_show_renders_no_submission_notice(): void
    {
        $assignment = Assignment::factory()->material()->create([
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'status' => 'published',
        ]);

        $this->actingAs($this->student)
            ->get($this->showUrl($assignment))
            ->assertOk()
            ->assertSee('ไม่ต้องส่งงาน');
    }

    public function test_assignment_show_renders_in_edit_tab(): void
    {
        $assignment = Assignment::factory()->file()->create([
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'status' => 'published',
        ]);

        $this->actingAs($this->teacher)
            ->get($this->showUrl($assignment).'?edit=1')
            ->assertOk()
            ->assertSee('แก้ไขงาน');
    }

    // ─────────────────────────────────────────────
    // Classroom work list — the poll-refreshed page
    // ─────────────────────────────────────────────

    public function test_classroom_work_page_renders_and_shows_a_newly_published_assignment(): void
    {
        $assignment = Assignment::factory()->file()->create([
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'status' => 'published',
            'title' => 'Freshly Published Homework',
        ]);
        Submission::create(['assignment_id' => $assignment->id, 'user_id' => $this->student->id, 'status' => 'assigned']);

        $this->actingAs($this->student)
            ->get(route('classroom.work', ['classroom' => $this->classroom->slug]))
            ->assertOk()
            ->assertSee('Freshly Published Homework');
    }

    public function test_creating_a_published_assignment_makes_it_appear_in_the_students_work_list(): void
    {
        // Exercises the real Create::save() path (not a factory shortcut) end
        // to end, matching a report of "created an assignment and it never
        // showed up" for students.
        Livewire::actingAs($this->teacher)
            ->test(Create::class, ['classroom' => $this->classroom])
            ->set('title', 'Brand New Worksheet')
            ->set('type', 'file')
            ->set('status', 'published')
            ->set('max_score', 100)
            ->call('save');

        $this->assertDatabaseHas('classwork_items', ['title' => 'Brand New Worksheet']);

        $this->actingAs($this->student)
            ->get(route('classroom.work', ['classroom' => $this->classroom->slug]))
            ->assertOk()
            ->assertSee('Brand New Worksheet');

        $this->actingAs($this->teacher)
            ->get(route('classroom.work', ['classroom' => $this->classroom->slug]))
            ->assertOk()
            ->assertSee('Brand New Worksheet');
    }

    public function test_classroom_work_page_hides_draft_assignment_from_student(): void
    {
        Assignment::factory()->file()->create([
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'status' => 'draft',
            'title' => 'Still A Draft',
        ]);

        $this->actingAs($this->student)
            ->get(route('classroom.work', ['classroom' => $this->classroom->slug]))
            ->assertOk()
            ->assertDontSee('Still A Draft');
    }
}
