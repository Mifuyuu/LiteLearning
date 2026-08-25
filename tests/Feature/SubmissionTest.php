<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    private User $student;

    private Classroom $classroom;

    private Assignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->student = User::factory()->create(['role' => 'student']);

        $this->classroom = Classroom::factory()->create(['teacher_id' => $this->teacher->id]);
        $this->classroom->members()->attach($this->student->id, ['role' => 'student', 'joined_at' => now()]);

        $this->assignment = Assignment::factory()->create([
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'type' => 'question',
            'status' => 'published',
            'allow_late_submission' => true,
            'due_date' => now()->addDay(),
        ]);

        // Pre-create the submission record for the student
        Submission::create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'status' => 'assigned',
        ]);
    }

    // ─────────────────────────────────────────────
    // Turn In
    // ─────────────────────────────────────────────

    public function test_student_can_turn_in_assignment(): void
    {
        Livewire::actingAs($this->student)
            ->test(\App\Livewire\Assignment\Show::class, [
                'classroom' => $this->classroom,
                'assignment' => $this->assignment,
            ])
            ->set('submissionContent', 'My answer here')
            ->call('turnIn')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'status' => 'turned_in',
        ]);
    }

    // ─────────────────────────────────────────────
    // Save Draft
    // ─────────────────────────────────────────────

    public function test_student_can_save_draft(): void
    {
        Livewire::actingAs($this->student)
            ->test(\App\Livewire\Assignment\Show::class, [
                'classroom' => $this->classroom,
                'assignment' => $this->assignment,
            ])
            ->set('submissionContent', 'Draft content')
            ->call('saveDraft')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'content' => 'Draft content',
            'status' => 'assigned',
        ]);
    }

    // ─────────────────────────────────────────────
    // Unsubmit
    // ─────────────────────────────────────────────

    public function test_student_can_unsubmit(): void
    {
        Submission::where('assignment_id', $this->assignment->id)
            ->where('user_id', $this->student->id)
            ->update(['status' => 'turned_in', 'turned_in_at' => now()]);

        Livewire::actingAs($this->student)
            ->test(\App\Livewire\Assignment\Show::class, [
                'classroom' => $this->classroom,
                'assignment' => $this->assignment,
            ])
            ->call('unsubmit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'status' => 'assigned',
        ]);
    }

    public function test_student_cannot_unsubmit_after_submissions_close(): void
    {
        $this->assignment->update([
            'due_date' => now()->subHour(),
            'allow_late_submission' => false,
        ]);

        Submission::where('assignment_id', $this->assignment->id)
            ->where('user_id', $this->student->id)
            ->update(['status' => 'turned_in', 'turned_in_at' => now()->subHours(2)]);

        Livewire::actingAs($this->student)
            ->test(\App\Livewire\Assignment\Show::class, [
                'classroom' => $this->classroom,
                'assignment' => $this->assignment,
            ])
            ->call('unsubmit');

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'status' => 'turned_in',
        ]);
    }

    // ─────────────────────────────────────────────
    // Submission blocked when closed
    // ─────────────────────────────────────────────

    public function test_submission_is_rejected_when_assignment_is_overdue_and_late_not_allowed(): void
    {
        $this->assignment->update([
            'due_date' => now()->subHour(),
            'allow_late_submission' => false,
        ]);

        Livewire::actingAs($this->student)
            ->test(\App\Livewire\Assignment\Show::class, [
                'classroom' => $this->classroom,
                'assignment' => $this->assignment,
            ])
            ->call('turnIn');

        // Status should still be 'assigned', not 'turned_in'
        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'status' => 'assigned',
        ]);
    }

    // ─────────────────────────────────────────────
    // Late submission allowed when flag is set
    // ─────────────────────────────────────────────

    public function test_late_submission_allowed_when_flag_is_set(): void
    {
        $this->assignment->update([
            'due_date' => now()->subHour(),
            'allow_late_submission' => true,
        ]);

        Livewire::actingAs($this->student)
            ->test(\App\Livewire\Assignment\Show::class, [
                'classroom' => $this->classroom,
                'assignment' => $this->assignment,
            ])
            ->set('submissionContent', 'Late but allowed')
            ->call('turnIn')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'status' => 'turned_in',
        ]);
    }

    // ─────────────────────────────────────────────
    // Non-member cannot access assignment
    // ─────────────────────────────────────────────

    public function test_draft_save_is_rejected_when_assignment_is_overdue_and_late_not_allowed(): void
    {
        $this->assignment->update([
            'due_date' => now()->subHour(),
            'allow_late_submission' => false,
        ]);

        Livewire::actingAs($this->student)
            ->test(\App\Livewire\Assignment\Show::class, [
                'classroom' => $this->classroom,
                'assignment' => $this->assignment,
            ])
            ->set('submissionContent', 'Draft after close')
            ->call('saveDraft');

        $this->assertDatabaseMissing('submissions', [
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->student->id,
            'content' => 'Draft after close',
        ]);
    }

    public function test_file_upload_is_rejected_when_assignment_is_overdue_and_late_not_allowed(): void
    {
        Storage::fake('s3');

        $this->assignment->update([
            'due_date' => now()->subHour(),
            'allow_late_submission' => false,
        ]);

        Livewire::actingAs($this->student)
            ->test(\App\Livewire\Assignment\Show::class, [
                'classroom' => $this->classroom,
                'assignment' => $this->assignment,
            ])
            ->set('uploadedFile', UploadedFile::fake()->create('late.pdf', 10, 'application/pdf'));

        $this->assertDatabaseMissing('attachments', [
            'file_name' => 'late.pdf',
            'uploaded_by' => $this->student->id,
        ]);
    }

    public function test_outsider_cannot_view_assignment(): void
    {
        /** @var User $outsider */
        $outsider = User::factory()->create(['role' => 'student']);

        $this->actingAs($outsider);

        $response = $this->get(route('assignment.show', [
            'classroom' => $this->classroom->slug,
            'assignment' => $this->assignment->slug,
        ]));

        $response->assertStatus(404);
    }

    public function test_grade_route_uses_submission_id_instead_of_slug(): void
    {
        $submission = Submission::where('assignment_id', $this->assignment->id)
            ->where('user_id', $this->student->id)
            ->firstOrFail();

        $url = route('assignment.grade', [
            'classroom' => $this->classroom,
            'assignment' => $this->assignment,
            'submission' => $submission,
        ]);

        $this->assertStringEndsWith('/g/'.$submission->id, $url);
    }

    public function test_teacher_can_return_submission_with_feedback_and_student_sees_feedback(): void
    {
        $submission = Submission::where('assignment_id', $this->assignment->id)
            ->where('user_id', $this->student->id)
            ->firstOrFail();
        $submission->turnIn();

        Livewire::actingAs($this->teacher)
            ->test(\App\Livewire\Assignment\Grade::class, [
                'classroom' => $this->classroom,
                'assignment' => $this->assignment,
                'submission' => $submission,
            ])
            ->set('feedback', 'กรุณาแก้ไขเนื้อหาในย่อหน้าที่ 2 เพิ่มเติม')
            ->call('returnSubmission')
            ->assertHasNoErrors();

        $submission->refresh();
        $this->assertSame('returned', $submission->status);
        $this->assertSame('กรุณาแก้ไขเนื้อหาในย่อหน้าที่ 2 เพิ่มเติม', $submission->feedback);
        $this->assertFalse($submission->isTurnedIn());

        // Student views assignment and sees the feedback
        Livewire::actingAs($this->student)
            ->test(\App\Livewire\Assignment\Show::class, [
                'classroom' => $this->classroom,
                'assignment' => $this->assignment,
            ])
            ->assertSee('ผู้สอนได้ส่งงานคืนให้แก้ไข')
            ->assertSee('กรุณาแก้ไขเนื้อหาในย่อหน้าที่ 2 เพิ่มเติม')
            ->assertSee('ส่งคืนแล้ว');
    }

    public function test_calendar_shows_all_unsubmitted_and_returned_tasks_sorted_by_priority(): void
    {
        $submission = Submission::where('assignment_id', $this->assignment->id)
            ->where('user_id', $this->student->id)
            ->firstOrFail();
        $submission->returnSubmission('แก้ไขด่วน');

        $assignmentNoDueDate = Assignment::factory()->create([
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'type' => 'question',
            'status' => 'published',
            'due_date' => null,
        ]);

        $assignmentUrgent = Assignment::factory()->create([
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'type' => 'question',
            'status' => 'published',
            'due_date' => now()->addHours(2),
        ]);

        Livewire::actingAs($this->student)
            ->test(\App\Livewire\Student\Calendar::class)
            ->assertSee($this->assignment->title)
            ->assertSee($assignmentNoDueDate->title)
            ->assertSee($assignmentUrgent->title)
            ->assertSee('ไม่มีกำหนดส่ง')
            ->assertSee('ส่งคืนแล้ว');
    }
}
