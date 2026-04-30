<?php

namespace App\Notifications;

use App\Models\MealPlanEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class DinnerReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public MealPlanEntry $entry,
    ) {}

    public function via(object $notifiable): array
    {
        if ($notifiable->wants('push', 'dinner_reminder') && ! $notifiable->isPushSuppressed()) {
            return [WebPushChannel::class];
        }

        return [];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $cooks = is_array($this->entry->assigned_cooks) ? $this->entry->assigned_cooks : [];
        $isCooking = in_array($notifiable->id, $cooks, true);
        $body = $isCooking ? "You're cooking" : 'On the menu';

        return (new WebPushMessage)
            ->title("Tonight: {$this->entry->display_title}")
            ->body($body)
            ->icon('/icons/icon-192.png')
            ->badge('/icons/badge-96.png')
            ->tag('dinner-'.$this->entry->date->toDateString())
            ->data([
                'type' => 'dinner_reminder',
                'url' => '/meals?date='.$this->entry->date->toDateString(),
                'entry_id' => $this->entry->id,
            ])
            ->options(['TTL' => 60 * 60 * 12]);
    }
}
