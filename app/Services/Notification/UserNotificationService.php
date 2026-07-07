<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\WelcomeMailNotification;

class UserNotificationService
{
    /**
     * Отправить письмо для подтверждения email
     */
    public function sendEmailVerification(User $user): void
    {
        $user->notify(new VerifyEmailNotification());
    }

    /**
     * Отправить приветственное письмо
     */
    //    public function sendWelcome(User $user): void
    //    {
    //        $user->notify(new WelcomeMailNotification($user));
    //    }
    public function sendWelcome(User $user): void
    {
        \Log::info('📨 sendWelcome started', ['email' => $user->email]);

        try {
            $user->notify(new WelcomeMailNotification($user));
            \Log::info('📨 notify() executed successfully');
        } catch (\Throwable $e) {
            \Log::error('❌ notify() failed', ['error' => $e->getMessage()]);
        }
    }

}
