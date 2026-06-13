<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Services\DashboardAnalyticsService;
use App\Services\GamificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function placeholder()
    {
        return view('livewire.placeholders.dashboard');
    }
    public function render()
    {
        /** @var User $user */
        $user = Auth::user();
        $classrooms = $user->allClassrooms();
        $classrooms->load('themeCategory');
        $analytics = app(DashboardAnalyticsService::class);

        $viewData = $user->isStudent()
            ? $this->studentViewData($user, $classrooms, $analytics)
            : $this->teacherViewData($user, $analytics);

        return view('livewire.dashboard', [
            'user' => $user,
            'classrooms' => $classrooms,
            ...$viewData,
        ]);
    }

    private function studentViewData(
        User $user,
        Collection $classrooms,
        DashboardAnalyticsService $analytics
    ): array {
        $classroomIds = $classrooms->pluck('id');
        $assignments = Assignment::query()
            ->with('classworkItem.classroom.themeCategory')
            ->whereHas('classworkItem', function ($query) use ($classroomIds): void {
                $query->whereIn('classroom_id', $classroomIds)
                    ->where(function ($publishQuery): void {
                        $publishQuery->whereNull('published_at')
                            ->orWhere('published_at', '<=', now());
                    });
            })
            ->published()
            ->whereNotIn('type', ['material', 'announcement', 'topic'])
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(4)
            ->get();

        $submissions = $user->submissions()
            ->with('assignment')
            ->whereIn('status', ['turned_in', 'graded', 'returned'])
            ->get();
        $scored = $submissions->whereNotNull('score');
        $onTimeCount = $submissions->filter(
            fn (Submission $submission): bool => ! $submission->isLate()
        )->count();
        $gamificationService = app(GamificationService::class);
        $currentLevelStartXp = $gamificationService->totalXpForLevel($user->level);
        $nextLevelXp = $gamificationService->totalXpForLevel($user->level + 1);
        $xpInCurrentLevel = max(0, $user->xp - $currentLevelStartXp);
        $xpNeededInLevel = max(1, $nextLevelXp - $currentLevelStartXp);
        $activity = $analytics->studentActivity($user);

        return [
            'role' => 'student',
            'activity' => $activity,
            'primaryMetric' => [
                'level' => $user->level,
                'xp_current' => $xpInCurrentLevel,
                'xp_required' => $xpNeededInLevel,
                'remaining' => max(0, $nextLevelXp - $user->xp),
                'progress_percent' => (int) min(100, round(($xpInCurrentLevel / $xpNeededInLevel) * 100)),
            ],
            'actionItems' => $assignments,
            'quickStats' => [
                ['label' => __('Coins'), 'value' => number_format($user->coins), 'icon' => 'star'],
                ['label' => __('Achievements'), 'value' => number_format($user->achievements()->count()), 'icon' => 'trophy'],
                ['label' => __('Completed'), 'value' => number_format($submissions->count()), 'icon' => 'check-circle'],
                ['label' => __('Average Score'), 'value' => number_format((float) ($scored->avg('score') ?? 0), 1), 'icon' => 'chart-bar'],
            ],
            'activitySummaries' => [
                ['label' => __('6-month activity'), 'value' => $activity['total']],
                ['label' => __('This week'), 'value' => $activity['current_week']],
                [
                    'label' => __('On-time submissions'),
                    'value' => $submissions->isNotEmpty()
                        ? (int) round(($onTimeCount / $submissions->count()) * 100).'%'
                        : '0%',
                ],
            ],
        ];
    }

    private function teacherViewData(User $user, DashboardAnalyticsService $analytics): array
    {
        $ownedClassrooms = $user->ownedClassrooms()
            ->where('is_archived', false)
            ->withCount(['students', 'classworkAssignments as assignments_count'])
            ->get();
        $classroomIds = $ownedClassrooms->pluck('id');
        $reviewProgress = $analytics->teacherReviewProgress($user);
        $activity = $analytics->teacherActivity($user);
        $reviewQueue = Assignment::query()
            ->with('classworkItem.classroom.themeCategory')
            ->withCount([
                'submissions as pending_count' => fn ($query) => $query->where('status', 'turned_in'),
            ])
            ->whereHas('classworkItem', fn ($query) => $query->whereIn('classroom_id', $classroomIds))
            ->whereHas('submissions', fn ($query) => $query->where('status', 'turned_in'))
            ->orderByDesc('pending_count')
            ->take(4)
            ->get();

        return [
            'role' => 'teacher',
            'activity' => $activity,
            'primaryMetric' => $reviewProgress,
            'actionItems' => $reviewQueue,
            'quickStats' => [
                ['label' => __('Classrooms'), 'value' => number_format($ownedClassrooms->count()), 'icon' => 'academic-cap'],
                ['label' => __('Students'), 'value' => number_format($ownedClassrooms->sum('students_count')), 'icon' => 'users'],
                ['label' => __('Assignments'), 'value' => number_format($ownedClassrooms->sum('assignments_count')), 'icon' => 'document-text'],
                ['label' => __('Pending Review'), 'value' => number_format($reviewProgress['pending']), 'icon' => 'clipboard-document-list'],
            ],
            'activitySummaries' => [
                ['label' => __('6-month activity'), 'value' => $activity['total']],
                ['label' => __('This week'), 'value' => $activity['current_week']],
                ['label' => __('Reviews this week'), 'value' => $reviewProgress['graded_this_week']],
            ],
        ];
    }
}
