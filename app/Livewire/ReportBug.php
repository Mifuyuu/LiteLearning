<?php

namespace App\Livewire;

use App\Models\BugReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReportBug extends Component
{
    public bool $showModal = false;
    public string $type = 'bug';
    public string $title = '';
    public string $message = '';

    protected $listeners = ['openReportModal' => 'openModal'];

    public function openModal(): void
    {
        $this->reset(['type', 'title', 'message']);
        $this->type = 'bug';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function submit(): void
    {
        $this->validate([
            'type'    => 'required|in:bug,suggestion,other',
            'title'   => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ]);

        BugReport::create([
            'user_id' => Auth::id(),
            'type'    => $this->type,
            'title'   => $this->title,
            'message' => $this->message,
            'status'  => 'pending',
        ]);

        $this->showModal = false;
        $this->dispatch('notify', message: __('report.success'));
    }

    public function render()
    {
        return view('livewire.report-bug');
    }
}
