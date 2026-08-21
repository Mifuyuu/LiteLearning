<?php

namespace App\Livewire\Admin;

use App\Services\AdminAnalyticsService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public string $period = '6m'; // 6m, 12m

    public function render()
    {
        $analytics = app(AdminAnalyticsService::class);

        $months = $this->period === '12m' ? 12 : 6;

        return view('livewire.admin.dashboard', [
            'stats' => $analytics->overview(),
            'growthData' => $analytics->userGrowth($months),
            'storageUsage' => $analytics->storageUsage(),
            'dailyActive' => $analytics->dailyActiveStudents(),
        ]);
    }
}
