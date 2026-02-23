<?php

namespace App\Livewire\Admin;

use App\Models\BugReport;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class BugReports extends Component
{
    public function toggleStatus(int $id): void
    {
        $report = BugReport::findOrFail($id);
        $report->update([
            'status' => $report->status === 'pending' ? 'resolved' : 'pending',
        ]);
    }

    public function render()
    {
        $reports = BugReport::with('user')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.admin.bug-reports', compact('reports'));
    }
}
