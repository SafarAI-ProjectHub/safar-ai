<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SyncFailed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('⚠️ فشل في تزامن المستخدم مع Moodle')
                    ->line($this->message)
                    ->action('عرض النظام', url('/admin/logs'))
                    ->line('يرجى التحقق من الخطأ وحل المشكلة بأسرع وقت ممكن.');
    }
}
