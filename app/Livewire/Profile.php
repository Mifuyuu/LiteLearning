<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserGamification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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

    public function mount(?User $user = null): void
    {
        /** @var User $viewer */
        $viewer = Auth::user();
        $user = ($user && $user->exists) ? $user : $viewer;
        $this->user = $user;

        $this->allAchievements = Achievement::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $this->unlockedAchievements = $user->achievements()
            ->orderByPivot('unlocked_at', 'desc')
            ->get();

        $classrooms = $user->allClassrooms();
        $classrooms->load(['teacher', 'themeCategory']);
        $classrooms->loadCount([
            'students',
            'assignments as assignments_count' => fn ($query) => $query->published(),
        ]);

        $this->profileClassrooms = $classrooms
            ->map(function ($classroom) use ($user): array {
                $role = 'นักเรียน';
                if ($classroom->isOwnedBy($user)) {
                    $role = 'เจ้าของ';
                } elseif ($classroom->pivot && $classroom->pivot->role === 'co-teacher') {
                    $role = 'ผู้สอนร่วม';
                }

                return [
                    'model' => $classroom,
                    'role' => $role,
                    'students_count' => $classroom->students_count,
                    'assignments_count' => $classroom->assignments_count,
                ];
            })
            ->values();

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
        $submissionsCount = $user->submissions()->whereIn('status', ['turned_in', 'graded', 'returned'])->count();

        $this->profileStats = [
            'level' => $user->level,
            'xp' => $user->xp,
            'coins' => $user->coins,
            'achievements' => $this->unlockedAchievements->count(),
            'achievement_total' => $this->allAchievements->count(),
            'classrooms' => $this->profileClassrooms->count(),
            'submissions' => $submissionsCount,
            'average_score' => round((float) ($gradedSubmissions->avg('score') ?? 0), 1),
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

        $ranks = array_column($points, 'rank');
        $minR = min($ranks);
        $maxR = max($ranks);
        $range = $maxR - $minR;

        foreach ($points as $index => $pt) {
            $x = ($index / (count($points) - 1)) * 400;
            $y = $range > 0 ? 20 + (($pt['rank'] - $minR) / $range) * 45 : 42.5;
            $this->chartPoints[] = [
                'x' => $x, 'y' => $y, 'day' => $pt['day'], 'rank' => $pt['rank'],
            ];
        }
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
