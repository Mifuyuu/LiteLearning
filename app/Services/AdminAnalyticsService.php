<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\BugReport;
use App\Models\Classroom;
use App\Models\CoinTransaction;
use App\Models\Submission;
use App\Models\StoreItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsService
{
    private function cacheKey(string $key): string
    {
        return "admin_analytics:{$key}";
    }

    /**
     * Overview stats for header cards.
     */
    public function overview(): array
    {
        $now = Carbon::now();

        return Cache::remember($this->cacheKey('overview'), 300, function () use ($now) {
            $totalUsers = User::count();
            $totalStudents = User::where('role', 'student')->count();
            $totalTeachers = User::where('role', 'teacher')->count();
            $totalClassrooms = Classroom::count();
            $activeClassrooms = Classroom::where('is_archived', false)->count();
            $totalAssignments = Assignment::count();
            $totalSubmissions = Submission::count();
            $pendingGrading = Submission::where('status', 'turned_in')->count();

            $coinsInEconomy = CoinTransaction::where('amount', '>', 0)->sum('amount');
            $coinsSpent = CoinTransaction::where('amount', '<', 0)->sum(DB::raw('ABS(amount)'));

            return [
                'total_users' => $totalUsers,
                'total_students' => $totalStudents,
                'total_teachers' => $totalTeachers,
                'new_users_month' => User::where('created_at', '>=', $now->copy()->startOfMonth())->count(),
                'total_classrooms' => $totalClassrooms,
                'active_classrooms' => $activeClassrooms,
                'total_assignments' => $totalAssignments,
                'total_submissions' => $totalSubmissions,
                'pending_grading' => $pendingGrading,
                'coins_in_economy' => $coinsInEconomy,
                'coins_spent' => $coinsSpent,
                'recent_bug_reports' => BugReport::latest()->take(5)->get(),
            ];
        });
    }

    /**
     * User growth data for charts.
     */
    public function userGrowth(int $months = 12): Collection
    {
        return Cache::remember($this->cacheKey("user_growth:{$months}"), 600, function () use ($months) {
            $start = Carbon::now()->subMonths($months - 1)->startOfMonth();
            $users = User::where('created_at', '>=', $start)
                ->pluck('created_at')
                ->map(fn ($d) => Carbon::parse($d)->format('Y-m'))
                ->countBy();

            $data = collect();
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $data->push([
                    'month' => $date->translatedFormat('M'),
                    'label' => $date->format('M Y'),
                    'count' => $users[$date->format('Y-m')] ?? 0,
                ]);
            }

            return $data;
        });
    }

    /**
     * Submission stats per classroom (top 10).
     */
    public function classroomActivity(int $limit = 10): Collection
    {
        return Cache::remember($this->cacheKey("classroom_activity:{$limit}"), 300, function () use ($limit) {
            return Classroom::where('is_archived', false)
                ->withCount(['classworkItems as assignments_count' => function ($q) {
                    $q->whereHas('assignment');
                }])
                ->withCount(['students'])
                ->get()
                ->map(function ($c) {
                    $submissions = Submission::whereHas('assignment.classworkItem', function ($q) use ($c) {
                        $q->where('classroom_id', $c->id);
                    });
                    $turnedIn = (clone $submissions)->where('status', '!=', 'assigned')->count();
                    $graded = (clone $submissions)->where('status', 'graded')->count();
                    $avgScore = (clone $submissions)->where('status', 'graded')->avg('score');

                    return [
                        'classroom' => $c,
                        'student_count' => $c->students_count,
                        'submissions' => $turnedIn,
                        'graded' => $graded,
                        'avg_score' => $avgScore ? round($avgScore, 1) : null,
                    ];
                })
                ->sortByDesc('submissions')
                ->take($limit)
                ->values();
        });
    }

    /**
     * Completion rate stats.
     */
    public function completionRate(): array
    {
        return Cache::remember($this->cacheKey('completion_rate'), 600, function () {
            $total = Submission::count();
            if ($total === 0) {
                return ['rate' => 0, 'turned_in' => 0, 'graded' => 0, 'returned' => 0, 'assigned' => 0];
            }

            $counts = Submission::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'turned_in' THEN 1 ELSE 0 END) as turned_in,
                SUM(CASE WHEN status = 'graded' THEN 1 ELSE 0 END) as graded,
                SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as returned,
                SUM(CASE WHEN status = 'assigned' THEN 1 ELSE 0 END) as assigned
            ")->first();

            return [
                'rate' => round((($counts->graded + $counts->returned) / $total) * 100, 1),
                'turned_in' => $counts->turned_in,
                'graded' => $counts->graded,
                'returned' => $counts->returned,
                'assigned' => $counts->assigned,
            ];
        });
    }

    /**
     * Store economy stats.
     */
    public function storeEconomy(): array
    {
        return Cache::remember($this->cacheKey('store_economy'), 600, function () {
            $totalEarned = CoinTransaction::where('amount', '>', 0)->sum('amount');
            $totalSpent = CoinTransaction::where('amount', '<', 0)->sum(DB::raw('ABS(amount)'));
            $totalTransactions = CoinTransaction::count();

            $popularItems = StoreItem::withCount(['users as purchase_count'])
                ->orderByDesc('purchase_count')
                ->take(5)
                ->get()
                ->map(fn ($item) => [
                    'name' => $item->name,
                    'count' => $item->purchase_count,
                ]);

            return [
                'total_earned' => $totalEarned,
                'total_spent' => $totalSpent,
                'total_transactions' => $totalTransactions,
                'popular_items' => $popularItems,
            ];
        });
    }

    /**
     * Top active users by submission count.
     */
    public function topActiveUsers(int $limit = 10): Collection
    {
        return Cache::remember($this->cacheKey("top_users:{$limit}"), 600, function () use ($limit) {
            return User::where('role', 'student')
                ->withCount('submissions')
                ->withSum('gamification as total_xp', 'xp')
                ->orderByDesc('submissions_count')
                ->take($limit)
                ->get()
                ->map(fn ($u) => [
                    'user' => $u,
                    'submissions' => $u->submissions_count,
                    'xp' => $u->total_xp ?? 0,
                ]);
        });
    }

    /**
     * Daily active students for a date range.
     */
    public function dailyActiveStudents(int $days = 30): Collection
    {
        return Cache::remember($this->cacheKey("daily_active:{$days}"), 600, function () use ($days) {
            $start = Carbon::now()->subDays($days - 1)->startOfDay();

            $data = Submission::where('turned_in_at', '>=', $start)
                ->select(DB::raw('DATE(turned_in_at) as date'), DB::raw('COUNT(DISTINCT user_id) as active_count'))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('active_count', 'date');

            $result = collect();
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $result->push([
                    'date' => $date,
                    'label' => Carbon::parse($date)->translatedFormat('j M'),
                    'count' => $data[$date] ?? 0,
                ]);
            }

            return $result;
        });
    }

    public function flushCache(): void
    {
        Cache::tags(['admin_analytics'])->flush();
    }
}
