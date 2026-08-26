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
            ->subject('รหัสยืนยันสำหรับ LiteLearn')
            ->greeting('สวัสดี!')
            ->line('รหัสยืนยันสำหรับ LiteLearn ของคุณคือ:')
            ->line('**'.$this->otp.'**')
            ->line('รหัสนี้จะหมดอายุใน 5 นาที')
            ->line('หากคุณไม่ได้ทำรายการใน LiteLearn กรุณาเพิกเฉยต่ออีเมลนี้');
    }
}
