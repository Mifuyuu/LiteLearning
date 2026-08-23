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

    public function test_dashboard_progress_gradient_uses_brand_blue(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString(
            'linear-gradient(90deg, #2563eb, #3b82f6, #93c5fd)',
            $css
        );
        $this->assertStringNotContainsString(
            'linear-gradient(90deg, #7132f5, #a855f7, #c4b5fd)',
            $css
        );
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_student_activity_aggregates_events_into_six_month_range(): void
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

        $previousYearComment = Comment::create([
            'user_id' => $student->id,
            'commentable_type' => Assignment::class,
            'commentable_id' => $assignment->id,
            'content' => 'Previous year activity',
        ]);
        $previousYearComment->forceFill([
            'created_at' => Carbon::parse('2025-12-31 10:00:00'),
            'updated_at' => Carbon::parse('2025-12-31 10:00:00'),
        ])->save();

        $activity = app(DashboardAnalyticsService::class)->studentActivity($student);
        $yesterday = collect($activity['days'])->firstWhere('date', now()->subDay()->toDateString());

        $this->assertSame(2025, $activity['year']);
        $this->assertSame('2025-12-08', $activity['start_date']);
        $this->assertSame('2026-06-11', $activity['end_date']);
        $this->assertCount(189, $activity['days']);
        $this->assertSame(27, $activity['week_count']);
        $this->assertSame(3, $yesterday['count']);
        $this->assertSame(4, $activity['total']);
        $this->assertTrue(collect($activity['days'])->firstWhere('date', '2025-12-31')['is_in_year']);
        $this->assertFalse(collect($activity['days'])->firstWhere('date', '2026-06-12')['is_in_year']);
    }

    public function test_activity_calendar_correctly_builds_twenty_six_weeks_for_leap_year(): void
    {
        Carbon::setTestNow('2012-06-11 12:00:00');

        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);

        $activity = app(DashboardAnalyticsService::class)->studentActivity($student);

        $this->assertSame(2011, $activity['year']);
        $this->assertSame('2011-12-05', $activity['start_date']);
        $this->assertSame('2012-06-11', $activity['end_date']);
        $this->assertCount(196, $activity['days']);
        $this->assertSame(28, $activity['week_count']);
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

    public function test_student_dashboard_renders_student_cards(): void
    {
        /** @var User $student */
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-content-width="contained"', false)
            ->assertSee('data-dashboard-role="student"', false)
            ->assertSee('dashboard-liquid-progress', false)
            ->assertSee('dashboard-liquid-fill', false)
            ->assertSee('outline-2', false)
            ->assertSee('data-activity-heatmap', false)
            ->assertSee('data-activity-cell', false)
            ->assertSee('aspect-square', false)
            ->assertDontSee('shadow-sm', false)
            ->assertSee('เลเวลปัจจุบัน')
            ->assertSee('สถิติด่วน');
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
            ->assertSee('รอตรวจ');
    }
}
