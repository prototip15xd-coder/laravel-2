<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeMailNotification extends Notification
{
    use Queueable;

    public function __construct(private User $user)
    {

    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    //    public function toMail($notifiable): MailMessage
    //    {
    //        return (new MailMessage)
    //            ->subject('Добро пожаловать в Laravel Shop!')
    //            ->view('emails.welcome', ['user' => $this->user]);
    //    }
    public function toMail($notifiable): MailMessage
    {
        \Log::info('📧 toMail() called', ['email' => $notifiable->email]);

        return (new MailMessage())
            ->subject('Добро пожаловать в Laravel Shop!')
            ->view('emails.welcome', ['user' => $this->user]);
    }
}
