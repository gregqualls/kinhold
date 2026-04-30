<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public User $assignedBy,
    ) {}

    /**
     * Channels chosen per-user from the unified notification_preferences shape.
     * Push is suppressed during quiet hours / global mute (see User::isPushSuppressed).
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        if ($notifiable->wants('email', 'task_assigned')) {
            $channels[] = 'mail';
        }

        if ($notifiable->wants('push', 'task_assigned') && ! $notifiable->isPushSuppressed()) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appUrl = config('app.url');
        $dueText = $this->task->due_date
            ? "Due: {$this->task->due_date->format('M j, Y')}"
            : 'No due date';

        $pointsText = $this->task->getEffectivePoints() > 0
            ? " ({$this->task->getEffectivePoints()} points)"
            : '';

        return (new MailMessage)
            ->subject("New task assigned: {$this->task->title}")
            ->greeting("Hi {$notifiable->name}!")
            ->line("**{$this->assignedBy->name}** assigned you a new task{$pointsText}:")
            ->line("**{$this->task->title}**")
            ->when($this->task->description, function (MailMessage $message) {
                $message->line($this->task->description);
            })
            ->line($dueText)
            ->action('View Task', "{$appUrl}/tasks")
            ->line("You've got this!");
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $points = $this->task->getEffectivePoints();
        $body = $this->assignedBy->name.' assigned you a task'
            .($points > 0 ? " ({$points} pts)" : '');

        return (new WebPushMessage)
            ->title($this->task->title)
            ->body($body)
            ->icon('/icons/icon-192.png')
            ->badge('/icons/badge-96.png')
            ->tag('task-'.$this->task->id)
            ->data([
                'type' => 'task_assigned',
                'url' => '/tasks?focus='.$this->task->id,
                'task_id' => $this->task->id,
            ])
            ->options(['TTL' => 60 * 60 * 24]);
    }
}
