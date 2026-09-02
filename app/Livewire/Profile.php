<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserGamification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Profile extends Component
{
    public function placeholder()
    {
        return view('livewire.placeholders.profile');
    }

    public User $user;

    public Collection $allAchievements;

    public Collection $unlockedAchievements;

    public Collection $profileClassrooms;

    public Collection $recentSubmissions;

    public ?int $rank = null;

    public array $chartPoints = [];

    public array $profileStats = [];

    public bool $isOwnProfile = true;

    public function mount(?User $user = null): void
    {
        /** @var User $viewer */
        $viewer = Auth::user();
        $user = ($user && $user->exists) ? $user : $viewer;
        $this->user = $user;
        $this->isOwnProfile = $user->id === $viewer->id;

        $this->allAchievements = Achievement::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $this->unlockedAchievements = $user->achievements()
            ->orderByPivot('unlocked_at', 'desc')
            ->get();

        $classrooms = $user->allClassrooms();
        $classrooms->load(['teacher', 'themeCategory']);
        $classrooms->loadCount(['assignments as assignments_count' => fn ($query) => $query->published()]);

        $this->profileClassrooms = $classrooms->values();

        $this->recentSubmissions = $user->submissions()
            ->with(['assignment.classworkItem.classroom.themeCategory'])
            ->whereNotNull('turned_in_at')
            ->latest('turned_in_at')
            ->take(6)
            ->get();

        $this->computeProfileStats($user);
        $this->computeChart($user);
    }

    private function computeProfileStats(User $user): void
    {
        $gradedSubmissions = $user->submissions()->whereNotNull('score');
        $submissionsCount = $user->submissions()->whereIn('status', ['turned_in', 'graded'])->count();

        $teacherStats = ['assignments_created' => 0, 'graded_submissions' => 0];
        if ($user->isTeacher()) {
            $classroomIds = $user->allClassrooms()->pluck('id');
            $teacherStats['assignments_created'] = \App\Models\Assignment::query()
                ->whereHas('classworkItem', fn ($q) => $q->whereIn('classroom_id', $classroomIds))
                ->count();
            $teacherStats['graded_submissions'] = \App\Models\Submission::query()
                ->whereHas('assignment.classworkItem', fn ($q) => $q->whereIn('classroom_id', $classroomIds))
                ->whereNotNull('score')
                ->count();
        }

        $gamificationService = app(\App\Services\GamificationService::class);
        $currentLevelStartXp = $gamificationService->totalXpForLevel($user->level);
        $nextLevelXp = $gamificationService->totalXpForLevel($user->level + 1);
        $xpInCurrentLevel = max(0, $user->xp - $currentLevelStartXp);
        $xpNeededInLevel = max(1, $nextLevelXp - $currentLevelStartXp);

        $this->profileStats = [
            'level' => $user->level,
            'xp' => $user->xp,
            'xp_current' => $xpInCurrentLevel,
            'xp_required' => $xpNeededInLevel,
            'xp_remaining' => max(0, $nextLevelXp - $user->xp),
            'level_progress_percent' => (int) min(100, round(($xpInCurrentLevel / $xpNeededInLevel) * 100)),
            'coins' => $user->coins,
            'achievements' => $this->unlockedAchievements->count(),
            'achievement_total' => $this->allAchievements->count(),
            'classrooms' => $this->profileClassrooms->count(),
            'submissions' => $submissionsCount,
            'average_score' => round((float) ($gradedSubmissions->avg('score') ?? 0), 1),
            'assignments_created' => $teacherStats['assignments_created'],
            'graded_submissions' => $teacherStats['graded_submissions'],
        ];
    }

    private function computeChart(User $user): void
    {
        $this->rank = null;
        $this->chartPoints = [];

        if (! $user->isStudent()) return;

        $userGamification = $user->gamification;
        if ($userGamification) {
            $this->rank = UserGamification::query()
                ->whereHas('user', fn ($q) => $q->where('role', 'student'))
                ->where(function ($query) use ($userGamification) {
                    $query->where('level', '>', $userGamification->level)
                        ->orWhere(function ($q) use ($userGamification) {
                            $q->where('level', '=', $userGamification->level)
                                ->where('xp', '>', $userGamification->xp);
                        });
                })
                ->count() + 1;
        } else {
            $this->rank = UserGamification::query()
                ->whereHas('user', fn ($q) => $q->where('role', 'student'))
                ->count() + 1;
        }

        $allXps = UserGamification::query()
            ->whereHas('user', fn ($q) => $q->where('role', 'student'))
            ->pluck('xp')
            ->sort()
            ->values()
            ->toArray();

        $events = [];

        $enrolledClassrooms = $user->enrolledClassrooms()->withPivot('joined_at')->get();
        foreach ($enrolledClassrooms as $classroom) {
            $date = $classroom->pivot->joined_at ?? $classroom->pivot->created_at ?? $classroom->created_at;
            if ($date) {
                $events[] = ['date' => \Illuminate\Support\Carbon::parse($date), 'xp' => 25];
            }
        }

        $submissions = $user->submissions()->whereNotNull('turned_in_at')->get();
        foreach ($submissions as $submission) {
            $events[] = [
                'date' => \Illuminate\Support\Carbon::parse($submission->turned_in_at),
                'xp' => 20,
            ];
        }

        $achievementsPivot = $user->achievements()->withPivot('unlocked_at')->get();
        foreach ($achievementsPivot as $achievement) {
            $date = $achievement->pivot->unlocked_at ?? $achievement->pivot->created_at ?? $achievement->created_at;
            if ($date) {
                $events[] = [
                    'date' => \Illuminate\Support\Carbon::parse($date),
                    'xp' => (int) ($achievement->xp_reward ?? 100),
                ];
            }
        }

        $currentXp = $user->xp;
        $points = [];

        for ($i = 0; $i <= 90; $i++) {
            $day = 90 - $i;
            $targetDate = \Illuminate\Support\Carbon::now()->subDays($day)->endOfDay();
            $xpOnDay = $currentXp;
            foreach ($events as $event) {
                if ($event['date']->greaterThan($targetDate)) {
                    $xpOnDay -= $event['xp'];
                }
            }
            if ($xpOnDay < 0) $xpOnDay = 0;

            $count = count($allXps);
            if ($count > 0) {
                $low = 0; $high = $count - 1; $pos = $count;
                while ($low <= $high) {
                    $mid = (int) (($low + $high) / 2);
                    if ($allXps[$mid] > $xpOnDay) { $pos = $mid; $high = $mid - 1; }
                    else { $low = $mid + 1; }
                }
                $rankOnDay = $count - $pos + 1;
            } else {
                $rankOnDay = 1;
            }

            $points[] = [
                'day' => $day === 0 ? 'วันนี้' : $day . ' วันที่แล้ว',
                'rank' => $rankOnDay,
            ];
        }

        $this->chartPoints = $points;
    }

    public function updateBio(string $bio): void
    {
        if (! $this->isOwnProfile) {
            return;
        }

        $bio = trim($bio);

        Validator::make(['bio' => $bio], [
            'bio' => 'nullable|string|max:250',
        ])->validate();

        $this->user->update(['bio' => $bio !== '' ? $bio : null]);
    }

    public function render(): View
    {
        return view('livewire.profile', [
            'achievements' => $this->allAchievements,
            'unlockedAchievements' => $this->unlockedAchievements,
            'unlockedAchievementIds' => $this->unlockedAchievements->pluck('id')->flip(),
            'profileClassrooms' => $this->profileClassrooms,
            'recentSubmissions' => $this->recentSubmissions,
            'profileStats' => $this->profileStats,
            'rank' => $this->rank,
            'chartPoints' => $this->chartPoints,
        ]);
    }
}
