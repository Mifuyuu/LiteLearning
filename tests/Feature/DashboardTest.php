<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Comment;
use App\Models\Submission;
use App\Models\User;
use App\Services\DashboardAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_student_activity_aggregates_events_into_one_year(): void
    {
        Carbon::setTestNow('2026-06-11 12:00:00');

        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $classroom->students()->attach($student, ['role' => 'student']);
        $assignment = Assignment::factory()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
        ]);

        Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'status' => 'graded',
            'turned_in_at' => now()->subDay()->setTime(9, 0),
            'graded_at' => now()->subDay()->setTime(15, 0),
        ]);

        $comment = Comment::create([
            'user_id' => $student->id,
            'commentable_type' => Assignment::class,
            'commentable_id' => $assignment->id,
            'content' => 'Done',
        ]);
        $comment->forceFill([
            'created_at' => now()->subDay()->setTime(10, 0),
            'updated_at' => now()->subDay()->setTime(10, 0),
        ])->save();

        $oldComment = Comment::create([
            'user_id' => $student->id,
            'commentable_type' => Assignment::class,
            'commentable_id' => $assignment->id,
            'content' => 'Old activity',
        ]);
        $oldComment->forceFill([
            'created_at' => now()->subWeeks(54),
            'updated_at' => now()->subWeeks(54),
        ])->save();

        $activity = app(DashboardAnalyticsService::class)->studentActivity($student);
        $yesterday = collect($activity['days'])->firstWhere('date', now()->subDay()->toDateString());

        $this->assertCount(371, $activity['days']);
        $this->assertSame(53, $activity['week_count']);
        $this->assertSame(3, $yesterday['count']);
        $this->assertSame(3, $activity['total']);
    }

    public function test_teacher_activity_and_review_progress_use_owned_classrooms(): void
    {
        Carbon::setTestNow('2026-06-11 12:00:00');

        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);
        $classroom = Classroom::factory()->create(['teacher_id' => $teacher->id]);
        $assignment = Assignment::factory()->create([
            'classroom_id' => $classroom->id,
            'user_id' => $teacher->id,
        ]);

        Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'status' => 'graded',
            'turned_in_at' => now()->subDays(2),
            'graded_at' => now()->subDay(),
        ]);

        Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => User::factory()->create()->id,
            'status' => 'turned_in',
            'turned_in_at' => now(),
        ]);

        $analytics = app(DashboardAnalyticsService::class);
        $activity = $analytics->teacherActivity($teacher);
        $review = $analytics->teacherReviewProgress($teacher);

        $this->assertGreaterThanOrEqual(2, $activity['total']);
        $this->assertSame(1, $review['pending']);
        $this->assertSame(1, $review['graded_this_week']);
        $this->assertSame(50, $review['progress_percent']);
    }

    public function test_student_dashboard_renders_student_single_viewport_cards(): void
    {
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-content-width="full"', false)
            ->assertSee('data-dashboard-role="student"', false)
            ->assertSee('data-activity-heatmap', false)
            ->assertSee(__('Current Level'))
            ->assertSee(__('Quick Stats'));
    }

    public function test_teacher_dashboard_renders_review_focused_cards(): void
    {
        /** @var User $teacher */
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-dashboard-role="teacher"', false)
            ->assertSee('data-activity-heatmap', false)
            ->assertSee(__('Pending Review'))
            ->assertSee(__('Review Queue'));
    }
}
