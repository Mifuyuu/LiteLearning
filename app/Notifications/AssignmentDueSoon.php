<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentDueSoon extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Assignment $assignment,
        private readonly string $remaining,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $classroom = $this->assignment->classworkItem->classroom;
        $url = route('assignment.show', [
            'classroom' => $classroom,
            'assignment' => $this->assignment,
        ]);

        return (new MailMessage)
            ->subject('งานใกล้ถึงกำหนดส่ง! — ' . $this->assignment->title)
            ->greeting('สวัสดี ' . ($notifiable->name ?? 'นักเรียน') . '!')
            ->line('งานต่อไปนี้กำลังจะถึงกำหนดส่ง:')
            ->line('**' . $this->assignment->title . '**')
            ->line('วิชา: ' . $classroom->name)
            ->line('กำหนดส่ง: ' . $this->assignment->due_date->translatedFormat('j M Y, H:i'))
            ->line('เหลือเวลา: ' . $this->remaining)
            ->action('ไปส่งงาน', $url)
            ->line('อย่าลืมส่งงานก่อนกำหนดเวลา!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'assignment_id' => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'due_date' => $this->assignment->due_date?->toIso8601String(),
        ];
    }
}
