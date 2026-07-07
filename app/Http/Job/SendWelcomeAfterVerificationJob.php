<?php

declare(strict_types=1);

namespace App\Http\Job;

use App\Models\User;
use App\Services\Notification\UserNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWelcomeAfterVerificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        private readonly int $userId
    ) {
        $this->onQueue('users.notifications.welcome');
    }

    public function handle(
        UserNotificationService $notificationService
    ): void {

        $user = User::query()->find($this->userId);

        if (!$user) {
            \Log::error('❌ Welcome Job: User not found', ['user_id' => $this->userId]);
            return;
        }

        \Log::info('✅ Welcome Job: User found, sending email', ['email' => $user->email]);

        $notificationService->sendWelcome($user);

        \Log::info('✅ Welcome Job: sendWelcome() finished');

    }
}
