<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Classroom;
use App\Models\CoinTransaction;
use App\Models\Submission;
use App\Models\User;
use App\Models\UserGamification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsService
{
    public const STORAGE_LIMIT_BYTES = 1073741824; // 1 GB per user

    public const TOTAL_STORAGE_LIMIT_BYTES = 1099511627776; // 1 TB system-wide

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
            $totalXp = UserGamification::sum('xp');

            $coinsInEconomy = CoinTransaction::where('amount', '>', 0)->sum('amount');
            $coinsSpent = CoinTransaction::where('amount', '<', 0)->sum(DB::raw('ABS(amount)'));

            return [
                'total_users' => $totalUsers,
                'total_students' => $totalStudents,
                'total_teachers' => $totalTeachers,
                'new_users_month' => User::where('created_at', '>=', $now->copy()->startOfMonth())->count(),
                'total_classrooms' => $totalClassrooms,
                'active_classrooms' => $activeClassrooms,
                'total_xp' => $totalXp,
                'coins_in_economy' => $coinsInEconomy,
                'coins_spent' => $coinsSpent,
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
     * Aggregate file storage usage across all users.
     */
    public function storageUsage(): array
    {
        return Cache::remember($this->cacheKey('storage_usage'), 300, function () {
            $usedBytes = (int) Attachment::sum('file_size');
            $totalBytes = self::TOTAL_STORAGE_LIMIT_BYTES;

            return [
                'used_bytes' => $usedBytes,
                'total_bytes' => $totalBytes,
                'percent' => $totalBytes > 0 ? min(100, round(($usedBytes / $totalBytes) * 100, 1)) : 0,
            ];
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
