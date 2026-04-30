<?php

namespace App\Notifications;

use App\Models\FamilyEvent;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class CalendarEventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public FamilyEvent $event,
        public Carbon $occurrenceAt,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = [];

        if ($notifiable->wants('email', 'calendar_event_reminder')) {
            $channels[] = 'mail';
        }

        if ($notifiable->wants('push', 'calendar_event_reminder') && ! $notifiable->isPushSuppressed()) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appUrl = config('app.url');
        $timeLine = 'Starts at '.$this->occurrenceAt->format('M j, g:i A');

        $message = (new MailMessage)
            ->subject("Starting soon: {$this->event->title}")
            ->greeting("Hi {$notifiable->name}!")
            ->line('A reminder for an upcoming event:')
            ->line("**{$this->event->title}**")
            ->line($timeLine);

        if ($this->event->location) {
            $message->line("Location: {$this->event->location}");
        }

        if ($this->event->recurrence_label) {
            $message->line($this->event->recurrence_label);
        }

        return $message->action('View Calendar', "{$appUrl}/calendar");
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $minutesBefore = (int) ($this->event->reminder_minutes_before ?? 0);
        $body = "In {$minutesBefore} minutes";
        if ($this->event->location) {
            $body .= " · {$this->event->location}";
        }

        return (new WebPushMessage)
            ->title($this->event->title)
            ->body($body)
            ->icon('/icons/icon-192.png')
            ->badge('/icons/badge-96.png')
            ->tag('event-'.$this->event->id.'-'.$this->occurrenceAt->toDateString())
            ->data([
                'type' => 'calendar_event_reminder',
                'url' => '/calendar?event='.$this->event->id,
                'event_id' => $this->event->id,
                'occurrence_date' => $this->occurrenceAt->toDateString(),
            ])
            ->options(['TTL' => 60 * 60 * 24]);
    }
}
