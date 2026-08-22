<?php

namespace App\Livewire;

use App\Models\BugReport;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReportBug extends Component
{
    public bool $showModal = false;

    public string $view = 'form';

    public string $type = 'bug';

    public string $title = '';

    public string $message = '';

    protected $listeners = ['openReportModal' => 'openModal'];

    public function mount(): void
    {
        $unread = Auth::user()->bugReports()
            ->whereNotNull('admin_reply')
            ->whereNull('read_at')
            ->get();

        if ($unread->isNotEmpty()) {
            // ponytail: two tabs navigating simultaneously could both read read_at IS NULL before either write lands, causing a duplicate toast — not worth locking for
            BugReport::whereIn('id', $unread->pluck('id'))->update(['read_at' => now()]);
            $this->dispatch('notify', message: __('messages.report.reply_received'));
        }
    }

    public function openModal(): void
    {
        $this->reset(['type', 'title', 'message']);
        $this->type = 'bug';
        $this->view = 'form';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function submit(): void
    {
        $settings = app(SettingsService::class);

        if (! $settings->bool('bug_report_enabled', true)) {
            $this->dispatch('notify', message: __('messages.report.closed'), type: 'error');

            return;
        }

        // I5: rate limit bug reports (configurable, default 3 per 10 minutes)
        $limit = $settings->int('bug_report_rate_limit', 3);
        $key = 'report-bug:'.Auth::id();
        if (cache()->has($key) && cache()->get($key) >= $limit) {
            $this->dispatch('notify', message: __('messages.report.throttle'));

            return;
        }
        cache()->increment($key);
        cache()->put($key, cache()->get($key), 600);

        $this->validate([
            'type' => 'required|in:bug,suggestion,other',
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ], [
            'type.required' => __('messages.validation.type_report'),
            'title.required' => __('messages.validation.title_report'),
            'message.required' => __('messages.validation.description'),
        ]);

        BugReport::create([
            'user_id' => Auth::id(),
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'status' => 'pending',
        ]);

        $this->showModal = false;
        $this->dispatch('notify', message: __('messages.report.success'));
    }

    public function render()
    {
        $reports = $this->view === 'history'
            ? Auth::user()->bugReports()->latest()->get()
            : collect();

        return view('livewire.report-bug', ['reports' => $reports]);
    }
}
