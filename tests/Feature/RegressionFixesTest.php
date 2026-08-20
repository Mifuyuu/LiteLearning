<?php

namespace Tests\Feature;

use App\Livewire\Assignment\Attendance as AssignmentAttendance;
use App\Livewire\Assignment\Create as AssignmentCreate;
use App\Livewire\Assignment\Show as AssignmentShow;
use App\Livewire\Classroom\Settings as ClassroomSettings;
use App\Livewire\Classroom\Stream as ClassroomStream;
use App\Livewire\Classroom\StreamComment;
use App\Livewire\Material\Create as MaterialCreate;
use App\Livewire\Material\Show as MaterialShow;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Attachment;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\ClassworkItem;
use App\Models\Comment;
use App\Models\Material;
use App\Models\Submission;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RegressionFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_material_create_succeeds_without_slug_error(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);

        $test = Livewire::actingAs($teacher)
            ->test(MaterialCreate::class, ['classroom' => $classroom])
            ->set('title', 'Course Guide')
            ->set('description', 'Material body')
            ->call('save');

        $material = \App\Models\Material::query()->firstOrFail();
        $test->assertJs('window.location.replace('.json_encode(route('material.show', ['classroom' => $classroom, 'material' => $material])).')');

        $this->assertDatabaseHas('classwork_items', [
            'classroom_id' => $classroom->id,
            'type' => 'material',
            'title' => 'Course Guide',
        ]);
        $this->assertDatabaseCount('materials', 1);
    }

    public function test_assignment_create_persists_uploaded_attachments(): void
    {
        Storage::fake('s3');

        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $file = UploadedFile::fake()->create('outline.pdf', 20, 'application/pdf');

        $test = Livewire::actingAs($teacher)
            ->test(AssignmentCreate::class, ['classroom' => $classroom])
            ->set('title', 'Essay 1')
            ->set('description', 'Write an essay')
            ->set('type', 'file')
            ->set('file', $file)
            ->call('save');

        /** @var Assignment $assignment */
        $assignment = Assignment::query()->firstOrFail();
        $test->assertJs('window.location.replace('.json_encode(route('assignment.show', ['classroom' => $classroom, 'assignment' => $assignment])).')');

        $this->assertDatabaseHas('attachments', [
            'attachable_type' => Assignment::class,
            'attachable_id' => $assignment->id,
            'file_name' => 'outline.pdf',
        ]);
        $this->assertCount(1, $assignment->attachments);
    }

    public function test_non_student_cannot_turn_in_assignment(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $assignment = Assignment::factory()->question()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'status' => 'published',
        ]);

        Livewire::actingAs($teacher)
            ->test(AssignmentShow::class, ['classroom' => $classroom, 'assignment' => $assignment])
            ->call('turnIn')
            ->assertStatus(403);

        $this->assertDatabaseMissing('submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $teacher->id,
        ]);
    }

    public function test_stream_comment_returns_404_for_inaccessible_announcement(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $outsider */
        $outsider = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);

        $classworkItem = ClassworkItem::factory()->forAnnouncement()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Private announcement',
        ]);

        $announcement = Announcement::create([
            'classwork_item_id' => $classworkItem->id,
            'content' => 'Visible only to members',
        ]);

        Livewire::actingAs($outsider)
            ->test(StreamComment::class, ['announcementId' => $announcement->id])
            ->assertStatus(404);
    }

    public function test_user_can_delete_their_own_stream_comment(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $classworkItem = ClassworkItem::factory()->forAnnouncement()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Class discussion',
        ]);

        $announcement = Announcement::create([
            'classwork_item_id' => $classworkItem->id,
            'content' => 'Share your thoughts',
        ]);

        $comment = Comment::create([
            'commentable_type' => Announcement::class,
            'commentable_id' => $announcement->id,
            'user_id' => $student->id,
            'content' => 'My own note',
        ]);

        Livewire::actingAs($student)
            ->test(StreamComment::class, ['announcementId' => $announcement->id])
            ->call('deleteComment', $comment->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_user_cannot_delete_another_users_stream_comment(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        /** @var User $classmate */
        $classmate = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);
        $classroom->members()->attach($classmate->id, ['role' => 'student', 'joined_at' => now()]);

        $classworkItem = ClassworkItem::factory()->forAnnouncement()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Class discussion',
        ]);

        $announcement = Announcement::create([
            'classwork_item_id' => $classworkItem->id,
            'content' => 'Share your thoughts',
        ]);

        $comment = Comment::create([
            'commentable_type' => Announcement::class,
            'commentable_id' => $announcement->id,
            'user_id' => $classmate->id,
            'content' => 'Not mine',
        ]);

        Livewire::actingAs($student)
            ->test(StreamComment::class, ['announcementId' => $announcement->id])
            ->call('deleteComment', $comment->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_co_teacher_cannot_save_classroom_settings(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role' => 'teacher']);
        /** @var User $coTeacher */
        $coTeacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create([
            'teacher_id' => $owner->id,
            'name' => 'Original Name',
        ]);

        $classroom->members()->attach($coTeacher->id, [
            'role' => 'co-teacher',
            'joined_at' => now(),
        ]);

        Livewire::actingAs($coTeacher)
            ->test(ClassroomSettings::class, ['classroom' => $classroom])
            ->assertStatus(403);

        $this->assertDatabaseHas('classrooms', [
            'id' => $classroom->id,
            'name' => 'Original Name',
        ]);
    }

    public function test_attendance_session_is_bound_to_assignment_classwork_item(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $assignment = Assignment::factory()->attendance()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'status' => 'published',
        ]);

        Livewire::actingAs($teacher)
            ->test(AssignmentAttendance::class, ['classroom' => $classroom, 'assignment' => $assignment])
            ->call('startSession')
            ->assertHasNoErrors();

        $session = AttendanceSession::query()->firstOrFail();

        $this->assertSame($assignment->classwork_item_id, $session->classwork_item_id);
        $this->assertTrue($assignment->fresh()->attendanceSession()->exists());
    }

    public function test_attendance_checkin_awards_rewards_only_once(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $assignment = Assignment::factory()->attendance()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'status' => 'published',
        ]);

        AttendanceSession::create([
            'classwork_item_id' => $assignment->classwork_item_id,
            'is_active' => true,
            'current_code' => '123456',
            'started_at' => now(),
            'code_rotated_at' => now(),
        ]);

        $component = Livewire::actingAs($student)
            ->test(AssignmentAttendance::class, ['classroom' => $classroom, 'assignment' => $assignment]);

        $component
            ->set('enteredCode', '123456')
            ->call('checkin')
            ->assertHasNoErrors();

        $component
            ->set('alreadyCheckedIn', false)
            ->set('enteredCode', '123456')
            ->call('checkin')
            ->assertHasNoErrors();

        $student->refresh();

        $this->assertEquals(50, $student->coins);
        $this->assertEquals(75, $student->xp);
        $this->assertDatabaseCount('coin_transactions', 1);
    }

    public function test_attendance_checkin_rejects_expired_code(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $assignment = Assignment::factory()->attendance()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'status' => 'published',
        ]);

        AttendanceSession::create([
            'classwork_item_id' => $assignment->classwork_item_id,
            'is_active' => true,
            'current_code' => '123456',
            'started_at' => now()->subMinute(),
            'code_rotated_at' => now()->subSeconds(16),
        ]);

        Livewire::actingAs($student)
            ->test(AssignmentAttendance::class, ['classroom' => $classroom, 'assignment' => $assignment])
            ->set('enteredCode', '123456')
            ->call('checkin');

        $this->assertDatabaseMissing('submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'status' => 'turned_in',
        ]);
        $this->assertSame(0, $student->fresh()->coins);
    }

    public function test_attendance_checkin_accepts_code_within_validity_window(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $assignment = Assignment::factory()->attendance()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'status' => 'published',
        ]);

        // Validity now matches rotation (10s, like a TOTP) — no grace period past it.
        // Freeze time so this doesn't flake under slow test-suite execution.
        $rotatedAt = now();
        AttendanceSession::create([
            'classwork_item_id' => $assignment->classwork_item_id,
            'is_active' => true,
            'current_code' => '123456',
            'started_at' => $rotatedAt->copy()->subMinute(),
            'code_rotated_at' => $rotatedAt,
        ]);

        $this->travelTo($rotatedAt->copy()->addSeconds(9));

        Livewire::actingAs($student)
            ->test(AssignmentAttendance::class, ['classroom' => $classroom, 'assignment' => $assignment])
            ->set('enteredCode', '123456')
            ->call('checkin');

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'status' => 'turned_in',
        ]);
    }

    public function test_attendance_checkin_is_rate_limited_after_ten_attempts(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $assignment = Assignment::factory()->attendance()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'status' => 'published',
        ]);

        AttendanceSession::create([
            'classwork_item_id' => $assignment->classwork_item_id,
            'is_active' => true,
            'current_code' => '123456',
            'started_at' => now(),
            'code_rotated_at' => now(),
        ]);

        $component = Livewire::actingAs($student)
            ->test(AssignmentAttendance::class, ['classroom' => $classroom, 'assignment' => $assignment]);

        for ($i = 0; $i < 10; $i++) {
            $component->set('enteredCode', '000000')->call('checkin');
        }

        $component->set('enteredCode', '123456')->call('checkin');

        $this->assertDatabaseMissing('submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'status' => 'turned_in',
        ]);
    }

    public function test_scheduled_announcement_is_hidden_from_students_until_publish_time(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $this->travelTo(now());

        $classworkItem = ClassworkItem::factory()->forAnnouncement()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Scheduled Notice',
            'published_at' => now()->addHour(),
        ]);

        Announcement::create([
            'classwork_item_id' => $classworkItem->id,
            'content' => 'Visible later',
        ]);

        Livewire::actingAs($student)
            ->test(ClassroomStream::class, ['classroom' => $classroom])
            ->assertDontSee('Visible later');

        $this->travelTo(now()->addHours(2));

        Livewire::actingAs($student)
            ->test(ClassroomStream::class, ['classroom' => $classroom])
            ->assertSee('Visible later');
    }

    public function test_scheduled_material_is_not_accessible_to_students_by_direct_url(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $classworkItem = ClassworkItem::factory()->forMaterial()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Scheduled Material',
            'published_at' => now()->addHour(),
        ]);

        $material = Material::create([
            'classwork_item_id' => $classworkItem->id,
        ]);

        Livewire::actingAs($student)
            ->test(MaterialShow::class, ['classroom' => $classroom, 'material' => $material])
            ->assertStatus(404);
    }

    public function test_student_cannot_comment_on_scheduled_material(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $classworkItem = ClassworkItem::factory()->forMaterial()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'title' => 'Scheduled Material',
            'published_at' => now()->addHour(),
        ]);

        $material = Material::create([
            'classwork_item_id' => $classworkItem->id,
        ]);

        Livewire::actingAs($student)
            ->test(StreamComment::class, [
                'contentId' => $material->id,
                'contentType' => Material::class,
            ])
            ->assertStatus(404);

        $this->assertDatabaseMissing('comments', [
            'commentable_type' => Material::class,
            'commentable_id' => $material->id,
        ]);
    }

    public function test_editing_assignment_preserves_existing_topic_when_left_unchanged(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $topic = Topic::create([
            'classroom_id' => $classroom->id,
            'name' => 'Unit 1',
        ]);

        $assignment = Assignment::factory()->question()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'status' => 'published',
            'topic_id' => $topic->id,
        ]);

        Livewire::actingAs($teacher)
            ->test(AssignmentShow::class, ['classroom' => $classroom, 'assignment' => $assignment])
            ->call('openEditTab')
            ->set('editTitle', 'Updated Assignment')
            ->call('saveAssignment')
            ->assertHasNoErrors();

        $this->assertSame($topic->id, $assignment->fresh()->classworkItem->topic_id);
    }

    public function test_editing_material_preserves_existing_topic_when_left_unchanged(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $topic = Topic::create([
            'classroom_id' => $classroom->id,
            'name' => 'Resources',
        ]);

        $classworkItem = ClassworkItem::factory()->forMaterial()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'topic_id' => $topic->id,
            'title' => 'Slides',
        ]);

        $material = Material::create([
            'classwork_item_id' => $classworkItem->id,
        ]);

        Livewire::actingAs($teacher)
            ->test(MaterialShow::class, ['classroom' => $classroom, 'material' => $material])
            ->call('openEditTab')
            ->set('editTitle', 'Updated Slides')
            ->call('saveMaterial')
            ->assertHasNoErrors();

        $this->assertSame($topic->id, $material->fresh()->classworkItem->topic_id);
    }

    public function test_deleting_assignment_removes_backing_classwork_item(): void
    {
        Storage::fake('s3');

        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $assignment = Assignment::factory()->question()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
            'status' => 'published',
        ]);
        $classworkItemId = $assignment->classwork_item_id;

        $submission = $assignment->submissions()->create([
            'user_id' => $student->id,
            'status' => 'assigned',
        ]);

        Attachment::create([
            'attachable_type' => Submission::class,
            'attachable_id' => $submission->id,
            'file_name' => 'work.pdf',
            'file_path' => 'submissions/work.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 1234,
            'uploaded_by' => $student->id,
        ]);

        Livewire::actingAs($teacher)
            ->test(AssignmentShow::class, ['classroom' => $classroom, 'assignment' => $assignment])
            ->call('deleteAssignment')
            ->assertRedirect();

        $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
        $this->assertDatabaseMissing('classwork_items', ['id' => $classworkItemId]);
    }
}
