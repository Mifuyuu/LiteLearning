<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Assignment;
use App\Models\CoinTransaction;
use App\Models\BugReport;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_users' => User::count(),
            'new_users_month' => User::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'total_classrooms' => Classroom::count(),
            'active_classrooms' => Classroom::where('is_archived', false)->count(),
            'total_assignments' => Assignment::count(),
            'store_transactions' => CoinTransaction::where('source', 'store_purchase')->count(),
            'recent_bug_reports' => BugReport::latest()->take(5)->get(),
        ];

        // Growth data for user chart (last 6 months)
        $growthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $growthData[] = [
                'month' => $date->translatedFormat('M'),
                'count' => User::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        }

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'growthData' => $growthData,
        ]);
    }
}
