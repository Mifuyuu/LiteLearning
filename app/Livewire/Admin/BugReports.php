<?php

namespace App\Livewire\Admin;

use App\Models\BugReport;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BugReports extends Component
{
    public array $replyDrafts = [];

    public function toggleStatus(int $id): void
    {
        $report = BugReport::findOrFail($id);
        $report->update([
            'status' => $report->status === 'pending' ? 'resolved' : 'pending',
        ]);
    }

    public function submitReply(int $id): void
    {
        $this->validate([
            "replyDrafts.$id" => 'required|string|max:2000',
        ], [
            "replyDrafts.$id.required" => __('messages.validation.description'),
        ]);

        // ponytail: single mutable "latest reply" per report, not a multi-message thread — upgrade to a bug_report_replies table if back-and-forth is ever needed
        BugReport::findOrFail($id)->update([
            'admin_reply' => $this->replyDrafts[$id],
            'replied_at' => now(),
            'read_at' => null,
        ]);

        unset($this->replyDrafts[$id]);
        $this->dispatch('notify', message: __('messages.admin.bug_report_replied'));
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
