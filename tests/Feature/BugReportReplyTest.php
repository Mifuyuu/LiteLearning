<?php

namespace Tests\Feature;

use App\Livewire\Admin\BugReports as AdminBugReports;
use App\Livewire\ReportBug;
use App\Models\BugReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BugReportReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reply_to_a_bug_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reporter = User::factory()->create(['role' => 'student']);

        $report = BugReport::create([
            'user_id' => $reporter->id,
            'type' => 'bug',
            'title' => 'ปุ่มกดไม่ได้',
            'message' => 'กดปุ่มส่งงานแล้วไม่มีอะไรเกิดขึ้น',
            'status' => 'pending',
        ]);

        Livewire::actingAs($admin)
            ->test(AdminBugReports::class)
            ->set("replyDrafts.{$report->id}", 'แก้ไขให้แล้วครับ ลองใหม่อีกครั้ง')
            ->call('submitReply', $report->id)
            ->assertDispatched('notify', message: __('messages.admin.bug_report_replied'));

        $report->refresh();

        $this->assertSame('แก้ไขให้แล้วครับ ลองใหม่อีกครั้ง', $report->admin_reply);
        $this->assertNotNull($report->replied_at);
        $this->assertNull($report->read_at);
        $this->assertSame('pending', $report->status);
    }

    public function test_mounting_report_bug_marks_unread_reply_as_read_and_notifies(): void
    {
        $reporter = User::factory()->create(['role' => 'student']);

        $report = BugReport::create([
            'user_id' => $reporter->id,
            'type' => 'bug',
            'title' => 'ปุ่มกดไม่ได้',
            'message' => 'กดปุ่มส่งงานแล้วไม่มีอะไรเกิดขึ้น',
            'status' => 'pending',
            'admin_reply' => 'แก้ไขให้แล้วครับ',
            'replied_at' => now(),
            'read_at' => null,
        ]);

        Livewire::actingAs($reporter)
            ->test(ReportBug::class)
            ->assertDispatched('notify', message: __('messages.report.reply_received'));

        $this->assertNotNull($report->fresh()->read_at);
    }
}
