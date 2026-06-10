<?php

namespace App\Services;

use App\Models\ClassworkItem;
use App\Models\Comment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardAnalyticsService
{
    private const ACTIVITY_WEEKS = 53;

    public function studentActivity(User $user): array
    {
        [$start, $end] = $this->activityRange();
        $events = collect();

        $submissions = $user->submissions()
            ->where(function ($query) use ($start, $end): void {
                $query->whereBetween('turned_in_at', [$start, $end])
                    ->orWhereBetween('graded_at', [$start, $end]);
            })
            ->get(['turned_in_at', 'graded_at']);

        foreach ($submissions as $submission) {
            if ($submission->turned_in_at?->between($start, $end)) {
                $events->push($submission->turned_in_at);
            }

            if ($submission->graded_at?->between($start, $end)) {
                $events->push($submission->graded_at);
            }
        }

        Comment::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->pluck('created_at')
            ->each(fn ($date) => $events->push(Carbon::parse($date)));

        return $this->buildActivity($events, $start);
    }

    public function teacherActivity(User $user): array
    {
        [$start, $end] = $this->activityRange();
        $classroomIds = $user->ownedClassrooms()->pluck('id');
        $events = collect();

        ClassworkItem::query()
            ->where('user_id', $user->id)
            ->whereIn('classroom_id', $classroomIds)
            ->whereBetween('created_at', [$start, $end])
            ->pluck('created_at')
            ->each(fn ($date) => $events->push(Carbon::parse($date)));

        Submission::query()
            ->whereNotNull('graded_at')
            ->whereBetween('graded_at', [$start, $end])
            ->whereHas('assignment.classworkItem', fn ($query) => $query->whereIn('classroom_id', $classroomIds))
            ->pluck('graded_at')
            ->each(fn ($date) => $events->push(Carbon::parse($date)));

        Comment::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$start, $end])
            ->pluck('created_at')
            ->each(fn ($date) => $events->push(Carbon::parse($date)));

        return $this->buildActivity($events, $start);
    }

    public function teacherReviewProgress(User $user): array
    {
        $classroomIds = $user->ownedClassrooms()->pluck('id');
        $submissions = Submission::query()
            ->whereHas('assignment.classworkItem', fn ($query) => $query->whereIn('classroom_id', $classroomIds));

        $pending = (clone $submissions)->where('status', 'turned_in')->count();
        $gradedThisWeek = (clone $submissions)
            ->whereBetween('graded_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $reviewTotal = $pending + $gradedThisWeek;

        return [
            'pending' => $pending,
            'graded_this_week' => $gradedThisWeek,
            'progress_percent' => $reviewTotal > 0
                ? (int) round(($gradedThisWeek / $reviewTotal) * 100)
                : 100,
        ];
    }

    private function activityRange(): array
    {
        $start = now()->startOfWeek()->subWeeks(self::ACTIVITY_WEEKS - 1)->startOfDay();
        $end = now()->endOfWeek()->endOfDay();

        return [$start, $end];
    }

    private function buildActivity(Collection $events, Carbon $start): array
    {
        $counts = $events
            ->map(fn ($date): string => Carbon::parse($date)->timezone(config('app.timezone'))->toDateString())
            ->countBy();
        $maximum = max(1, (int) $counts->max());
        $dayCount = self::ACTIVITY_WEEKS * 7;
        $days = collect(range(0, $dayCount - 1))->map(function (int $offset) use ($counts, $maximum, $start): array {
            $date = $start->copy()->addDays($offset);
            $count = (int) $counts->get($date->toDateString(), 0);

            return [
                'date' => $date->toDateString(),
                'label' => $date->translatedFormat('j M Y'),
                'count' => $count,
                'level' => $this->activityLevel($count, $maximum),
                'is_future' => $date->isFuture(),
            ];
        });

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $start->copy()->addDays($dayCount - 1)->toDateString(),
            'week_count' => self::ACTIVITY_WEEKS,
            'days' => $days->all(),
            'total' => $days->sum('count'),
            'current_week' => $days
                ->filter(fn (array $day): bool => Carbon::parse($day['date'])->isCurrentWeek())
                ->sum('count'),
        ];
    }

    private function activityLevel(int $count, int $maximum): int
    {
        if ($count === 0) {
            return 0;
        }

        return max(1, min(4, (int) ceil(($count / $maximum) * 4)));
    }
}
