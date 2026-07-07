<?php

declare(strict_types=1);

namespace App\Http\Job;

use App\Models\User;
use App\Services\Notification\UserNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class SendRegistrationVerificationJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        private readonly int $userId
    ) {
        $this->onQueue('users.notifications.verify');
    }

    public function handle(
        UserNotificationService $notificationService
    ): void {
        $user = User::query()->find($this->userId);

        if (!$user) {
            return;
        }

        $notificationService->sendEmailVerification($user);
    }
}
