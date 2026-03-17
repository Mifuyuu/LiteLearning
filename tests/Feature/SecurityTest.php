<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // IDOR: Assignment must belong to Classroom
    // ──────────────────────────────────────────────

    public function test_assignment_show_rejects_assignment_from_different_classroom(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroomA = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroomB = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $assignment = Assignment::factory()->create(['user_id' => $teacher->id, 'classroom_id' => $classroomB->id]);

        // Try to view classroomB's assignment via classroomA's URL
        $this->actingAs($teacher);

        $response = $this->get(route('assignment.show', [
            'classroom' => $classroomA->slug,
            'assignment' => $assignment->slug,
        ]));

        $response->assertStatus(404);
    }

    public function test_grade_rejects_submission_from_different_assignment(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);

        $assignmentA = Assignment::factory()->create(['user_id' => $teacher->id, 'classroom_id' => $classroom->id]);

        $assignmentB = Assignment::factory()->create(['user_id' => $teacher->id, 'classroom_id' => $classroom->id]);

        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom->members()->attach($student->id, ['role' => 'student', 'joined_at' => now()]);

        $submission = Submission::create([
            'assignment_id' => $assignmentB->id,
            'user_id' => $student->id,
            'status' => 'turned_in',
            'turned_in_at' => now(),
        ]);

        // Try to grade assignmentB's submission through assignmentA's URL
        $this->actingAs($teacher);

        $response = $this->get(route('assignment.grade', [
            'classroom' => $classroom->slug,
            'assignment' => $assignmentA->slug,
            'submission' => $submission->id,
        ]));

        $response->assertStatus(404);
    }

    // ──────────────────────────────────────────────
    // Cross-classroom announcement deletion
    // ──────────────────────────────────────────────

    public function test_teacher_cannot_delete_announcement_from_another_classroom(): void
    {
        /** @var User $teacherA */
        $teacherA = User::factory()->create(['role' => 'teacher']);
        /** @var User $teacherB */
        $teacherB = User::factory()->create(['role' => 'teacher']);

        $classroomA = Classroom::factory()->create(['teacher_id' => $teacherA->id]);
        $classroomB = Classroom::factory()->create(['teacher_id' => $teacherB->id]);

        $announcement = Announcement::factory()->create(['user_id' => $teacherB->id, 'classroom_id' => $classroomB->id]);

        // Teacher A tries to delete an announcement from Classroom B
        // by calling deleteAnnouncement on their own Classroom A component
        Livewire::actingAs($teacherA)
            ->test(\App\Livewire\Classroom\Show::class, ['classroom' => $classroomA])
            ->call('deleteAnnouncement', $announcement->id)
            ->assertStatus(404);
    }

    // ──────────────────────────────────────────────
    // Cross-classroom comment injection
    // ──────────────────────────────────────────────

    public function test_user_cannot_comment_on_announcement_they_have_no_access_to(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $outsider */
        $outsider = User::factory()->create(['role' => 'student']);

        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $announcement = Announcement::factory()->create(['user_id' => $teacher->id, 'classroom_id' => $classroom->id]);

        // Outsider (not enrolled) tries to comment via tampered announcementId
        Livewire::actingAs($outsider)
            ->test(\App\Livewire\Classroom\StreamComment::class, ['announcementId' => $announcement->id])
            ->set('commentText', 'Injected comment')
            ->call('addComment')
            ->assertStatus(403);
    }

    // ──────────────────────────────────────────────
    // Students cannot create classrooms
    // ──────────────────────────────────────────────

    public function test_student_cannot_create_classroom(): void
    {
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Classroom\Create::class)
            ->set('name', 'Hacked Classroom')
            ->call('create')
            ->assertStatus(403);
    }

    public function test_teacher_can_create_classroom(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);

        Livewire::actingAs($teacher)
            ->test(\App\Livewire\Classroom\Create::class)
            ->set('name', 'Legit Classroom')
            ->call('create')
            ->assertRedirect();

        $this->assertDatabaseHas('classrooms', ['name' => 'Legit Classroom']);
    }

    // ──────────────────────────────────────────────
    // Login rate limiting
    // ──────────────────────────────────────────────

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        User::factory()->create([
            'email' => 'victim@example.com',
            'role' => 'student',
        ]);

        $component = Livewire::test(\App\Livewire\Auth\Login::class);

        // Fire 5 bad attempts
        for ($i = 0; $i < 5; $i++) {
            $component
                ->set('email', 'victim@example.com')
                ->set('password', 'wrong-password')
                ->call('login');
        }

        // 6th attempt should hit rate limiter
        $component
            ->set('email', 'victim@example.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('email')
            ->assertSee('Too many');
    }

    public function test_login_redirects_student_to_dashboard(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        Livewire::test(
            \App\Livewire\Auth\Login::class
        )
            ->set('email', 'student@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('dashboard'));
    }

    public function test_login_redirects_admin_to_admin_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        Livewire::test(
            \App\Livewire\Auth\Login::class
        )
            ->set('email', 'admin@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));
    }
}
