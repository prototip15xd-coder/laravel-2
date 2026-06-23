<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage())
            ->subject('Подтвердите email на Laravel Shop')
            ->greeting('Здравствуйте, ' . $notifiable->name . '!')
            ->line('Вы зарегистрировались на нашем сайте.')
            ->line('Нажмите на кнопку, чтобы подтвердить адрес электронной почты:')
            ->action('Подтвердить email', $verificationUrl)
            ->line('Если вы не регистрировались, проигнорируйте это письмо.')
            ->salutation('С уважением, команда Laravel Shop');
    }

    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
