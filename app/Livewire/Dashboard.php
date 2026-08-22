<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Models\UserGamification;
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

    public string $role = 'student';

    public array $viewData = [];

    public Collection $classrooms;

    public function mount(): void
    {
        $this->refreshViewData();
    }

    private function refreshViewData(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->classrooms = $user->allClassrooms()->load('themeCategory');
        $analytics = app(DashboardAnalyticsService::class);

        if ($user->isStudent()) {
            $this->role = 'student';
            $this->viewData = $this->buildStudentView($user, $this->classrooms, $analytics, 6);
        } else {
            $this->role = 'teacher';
            $this->viewData = $this->buildTeacherView($user, $analytics, 6);
        }
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'role' => $this->role,
            'user' => Auth::user(),
            'classrooms' => $this->classrooms,
            ...$this->viewData,
        ]);
    }

    private function buildStudentView(
        User $user,
        Collection $classrooms,
        DashboardAnalyticsService $analytics,
        int $months = 6
    ): array {
        $submissions = $user->submissions()
            ->with('assignment')
            ->whereIn('status', ['turned_in', 'graded', 'returned'])
            ->get();
        $onTimeCount = $submissions->filter(
            fn (Submission $submission): bool => ! $submission->isLate()
        )->count();
        $classroomIds = $classrooms->pluck('id');
        $incompleteCount = Assignment::query()
            ->whereNotIn('type', ['material', 'announcement', 'topic'])
            ->published()
            ->whereHas('classworkItem', function ($query) use ($classroomIds): void {
                $query->whereIn('classroom_id', $classroomIds)
                    ->where(function ($publishQuery): void {
                        $publishQuery->whereNull('published_at')
                            ->orWhere('published_at', '<=', now());
                    });
            })
            ->whereDoesntHave('submissions', function ($query) use ($user): void {
                $query->where('user_id', $user->id)
                    ->whereIn('status', ['turned_in', 'graded', 'returned']);
            })
            ->count();
        $gamificationService = app(GamificationService::class);
        $currentLevelStartXp = $gamificationService->totalXpForLevel($user->level);
        $nextLevelXp = $gamificationService->totalXpForLevel($user->level + 1);
        $xpInCurrentLevel = max(0, $user->xp - $currentLevelStartXp);
        $xpNeededInLevel = max(1, $nextLevelXp - $currentLevelStartXp);
        $activity = $analytics->studentActivity($user, $months);
        $rank = 1 + UserGamification::query()
            ->join('users', 'users.id', '=', 'user_gamifications.user_id')
            ->where('users.role', 'student')
            ->where(function ($query) use ($user): void {
                $query->where('user_gamifications.level', '>', $user->level)
                    ->orWhere(function ($query) use ($user): void {
                        $query->where('user_gamifications.level', $user->level)
                            ->where('user_gamifications.xp', '>', $user->xp);
                    });
            })
            ->count();

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
            'quickStats' => [
                ['label' => 'เหรียญ', 'value' => number_format($user->coins), 'icon' => 'star'],
                ['label' => 'ความสำเร็จ', 'value' => number_format($user->achievements()->count()), 'icon' => 'trophy'],
                ['label' => 'ภารกิจที่ยังไม่สำเร็จ', 'value' => number_format($incompleteCount), 'icon' => 'exclamation-circle'],
                ['label' => 'อันดับปัจจุบัน', 'value' => '#'.number_format($rank), 'icon' => 'chart-bar'],
            ],
            'activitySummaries' => [
                ['label' => 'กิจกรรมในรอบ 6 เดือน', 'value' => $activity['total']],
                ['label' => 'สัปดาห์นี้', 'value' => $activity['current_week']],
                [
                    'label' => 'ส่งงานตรงเวลา',
                    'value' => $submissions->isNotEmpty()
                        ? (int) round(($onTimeCount / $submissions->count()) * 100).'%'
                        : '0%',
                ],
            ],
        ];
    }

    private function buildTeacherView(User $user, DashboardAnalyticsService $analytics, int $months = 6): array
    {
        $ownedClassrooms = $user->ownedClassrooms()
            ->where('is_archived', false)
            ->withCount(['students', 'classworkAssignments as assignments_count'])
            ->get();
        $reviewProgress = $analytics->teacherReviewProgress($user);
        $activity = $analytics->teacherActivity($user, $months);

        return [
            'role' => 'teacher',
            'activity' => $activity,
            'primaryMetric' => $analytics->teacherSubmissionStatus($user),
            'quickStats' => [
                ['label' => 'ห้องเรียน', 'value' => number_format($ownedClassrooms->count()), 'icon' => 'academic-cap'],
                ['label' => 'นักเรียน', 'value' => number_format($ownedClassrooms->sum('students_count')), 'icon' => 'users'],
                ['label' => 'งานที่มอบหมาย', 'value' => number_format($ownedClassrooms->sum('assignments_count')), 'icon' => 'document-text'],
                ['label' => 'รอตรวจ', 'value' => number_format($reviewProgress['pending']), 'icon' => 'clipboard-document-list'],
            ],
            'activitySummaries' => [
                ['label' => 'กิจกรรมในรอบ 6 เดือน', 'value' => $activity['total']],
                ['label' => 'สัปดาห์นี้', 'value' => $activity['current_week']],
                ['label' => 'ตรวจงานสัปดาห์นี้', 'value' => $reviewProgress['graded_this_week']],
            ],
        ];
    }
}
