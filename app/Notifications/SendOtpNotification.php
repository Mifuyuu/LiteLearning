<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpNotification extends Notification
{
    public function __construct(
        private readonly string $otp
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('รหัสยืนยันอีเมลของคุณ')
            ->greeting('สวัสดี!')
            ->line('รหัสยืนยันอีเมลของคุณคือ:')
            ->line('**'.$this->otp.'**')
            ->line('รหัสนี้จะหมดอายุใน 5 นาที')
            ->line('หากคุณไม่ได้สมัครสมาชิก กรุณาเพิกเฉยต่ออีเมลนี้');
    }
}
