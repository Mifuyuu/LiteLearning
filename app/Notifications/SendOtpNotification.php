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
            ->subject(__('รหัสยืนยันอีเมลของคุณ'))
            ->greeting(__('สวัสดี!'))
            ->line(__('รหัสยืนยันอีเมลของคุณคือ:'))
            ->line('**'.$this->otp.'**')
            ->line(__('รหัสนี้จะหมดอายุใน 10 นาที'))
            ->line(__('หากคุณไม่ได้สมัครสมาชิก กรุณาเพิกเฉยต่ออีเมลนี้'));
    }
}
