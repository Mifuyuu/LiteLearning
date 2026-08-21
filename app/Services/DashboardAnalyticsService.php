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
    public function studentActivity(User $user, int $months = 6): array
    {
        [$yearStart, $yearEnd, $gridStart, $gridEnd] = $this->activityRange($months);
        $events = collect();

        $submissions = $user->submissions()
            ->where(function ($query) use ($yearStart, $yearEnd): void {
                $query->whereBetween('turned_in_at', [$yearStart, $yearEnd])
                    ->orWhereBetween('graded_at', [$yearStart, $yearEnd]);
            })
            ->get(['turned_in_at', 'graded_at']);

        foreach ($submissions as $submission) {
            if ($submission->turned_in_at?->between($yearStart, $yearEnd)) {
                $events->push($submission->turned_in_at);
            }

            if ($submission->graded_at?->between($yearStart, $yearEnd)) {
                $events->push($submission->graded_at);
            }
        }

        Comment::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->pluck('created_at')
            ->each(fn ($date) => $events->push(Carbon::parse($date)));

        return $this->buildActivity($events, $yearStart, $yearEnd, $gridStart, $gridEnd);
    }

    public function teacherActivity(User $user, int $months = 6): array
    {
        [$yearStart, $yearEnd, $gridStart, $gridEnd] = $this->activityRange($months);
        $classroomIds = $user->ownedClassrooms()->pluck('id');
        $events = collect();

        ClassworkItem::query()
            ->where('user_id', $user->id)
            ->whereIn('classroom_id', $classroomIds)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->pluck('created_at')
            ->each(fn ($date) => $events->push(Carbon::parse($date)));

        Submission::query()
            ->whereNotNull('graded_at')
            ->whereBetween('graded_at', [$yearStart, $yearEnd])
            ->whereHas('assignment.classworkItem', fn ($query) => $query->whereIn('classroom_id', $classroomIds))
            ->pluck('graded_at')
            ->each(fn ($date) => $events->push(Carbon::parse($date)));

        Comment::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$yearStart, $yearEnd])
            ->pluck('created_at')
            ->each(fn ($date) => $events->push(Carbon::parse($date)));

        return $this->buildActivity($events, $yearStart, $yearEnd, $gridStart, $gridEnd);
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

    public function teacherSubmissionStatus(User $user): array
    {
        $classroomIds = $user->ownedClassrooms()->pluck('id');
        $submissions = Submission::query()
            ->whereHas('assignment.classworkItem', fn ($query) => $query->whereIn('classroom_id', $classroomIds));

        $total = (clone $submissions)->count();
        if ($total === 0) {
            return ['rate' => 0, 'turned_in' => 0, 'graded' => 0, 'returned' => 0, 'assigned' => 0];
        }

        $counts = (clone $submissions)->selectRaw("
            SUM(CASE WHEN status = 'turned_in' THEN 1 ELSE 0 END) as turned_in,
            SUM(CASE WHEN status = 'graded' THEN 1 ELSE 0 END) as graded,
            SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as returned,
            SUM(CASE WHEN status = 'assigned' THEN 1 ELSE 0 END) as assigned
        ")->first();

        return [
            'rate' => round((($counts->graded + $counts->returned) / $total) * 100, 1),
            'turned_in' => (int) $counts->turned_in,
            'graded' => (int) $counts->graded,
            'returned' => (int) $counts->returned,
            'assigned' => (int) $counts->assigned,
        ];
    }

    private function activityRange(int $months = 6): array
    {
        $gridEnd = now()->endOfWeek(Carbon::SUNDAY)->endOfDay();
        $gridStart = now()->subMonths($months)->startOfWeek(Carbon::MONDAY)->startOfDay();
        $rangeStart = $gridStart->copy();
        $rangeEnd = now()->endOfDay();

        return [$rangeStart, $rangeEnd, $gridStart, $gridEnd];
    }

    private function buildActivity(
        Collection $events,
        Carbon $yearStart,
        Carbon $yearEnd,
        Carbon $gridStart,
        Carbon $gridEnd
    ): array {
        $counts = $events
            ->map(fn ($date): string => Carbon::parse($date)->timezone(config('app.timezone'))->toDateString())
            ->countBy();
        $maximum = max(1, (int) $counts->max());
        $dayCount = $gridStart->diffInDays($gridEnd) + 1;
        $days = collect(range(0, $dayCount - 1))->map(function (int $offset) use (
            $counts,
            $maximum,
            $yearStart,
            $yearEnd,
            $gridStart
        ): array {
            $date = $gridStart->copy()->addDays($offset);
            $isInYear = $date->betweenIncluded($yearStart, $yearEnd);
            $count = $isInYear ? (int) $counts->get($date->toDateString(), 0) : 0;

            return [
                'date' => $date->toDateString(),
                'label' => $date->translatedFormat('j M Y'),
                'count' => $count,
                'level' => $this->activityLevel($count, $maximum),
                'is_in_year' => $isInYear,
                'is_future' => $date->isFuture(),
            ];
        });

        $monthLabels = [];
        $current = $gridStart->copy();
        $end = $gridEnd->copy();
        $lastMonth = null;
        while ($current->lte($end)) {
            $monthVal = $current->month;
            if ($monthVal !== $lastMonth) {
                $weekIndex = intdiv((int) $gridStart->diffInDays($current), 7) + 1;
                $monthLabels[] = [
                    'week' => $weekIndex,
                    'label' => $current->translatedFormat('M'),
                ];
                $lastMonth = $monthVal;
            }
            $current->addWeek();
        }

        return [
            'year' => $yearStart->year,
            'start_date' => $yearStart->toDateString(),
            'end_date' => $yearEnd->toDateString(),
            'grid_start_date' => $gridStart->toDateString(),
            'grid_end_date' => $gridEnd->toDateString(),
            'week_count' => (int) ($dayCount / 7),
            'days' => $days->all(),
            'total' => $days->sum('count'),
            'current_week' => $days
                ->filter(fn (array $day): bool => Carbon::parse($day['date'])->isCurrentWeek())
                ->sum('count'),
            'month_labels' => $monthLabels,
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
