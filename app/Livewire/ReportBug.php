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
        // I5: rate limit bug reports (3 per 10 minutes)
        $key = 'report-bug:'.Auth::id();
        if (cache()->has($key) && cache()->get($key) >= 3) {
            $this->dispatch('notify', message: 'คุณส่งรายงานบ่อยเกินไป กรุณารอสักครู่แล้วลองอีกครั้ง');

            return;
        }
        cache()->increment($key);
        cache()->put($key, cache()->get($key), 600);

        $this->validate([
            'type' => 'required|in:bug,suggestion,other',
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ]);

        BugReport::create([
            'user_id' => Auth::id(),
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'status' => 'pending',
        ]);

        $this->showModal = false;
        $this->dispatch('notify', message: 'ส่งรายงานสำเร็จ ขอบคุณสำหรับข้อมูล!');
    }

    public function render()
    {
        return view('livewire.report-bug');
    }
}
