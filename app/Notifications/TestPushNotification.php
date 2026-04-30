<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

/**
 * One-shot wrapper used by PushSubscriptionController::test() — bypasses
 * preference gating because the user explicitly clicked "send test push".
 */
class TestPushNotification extends Notification
{
    public function __construct(public WebPushMessage $message) {}

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        return $this->message;
    }
}
