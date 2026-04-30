<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TaskDueSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = [];

        if ($notifiable->wants('email', 'task_due_soon')) {
            $channels[] = 'mail';
        }

        if ($notifiable->wants('push', 'task_due_soon') && ! $notifiable->isPushSuppressed()) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appUrl = config('app.url');
        $dueText = $this->task->due_date
            ? "Due: {$this->task->due_date->format('M j, Y')}"
            : 'Due today';

        return (new MailMessage)
            ->subject("Reminder: {$this->task->title} is due today")
            ->greeting("Hi {$notifiable->name}!")
            ->line('This task is due today:')
            ->line("**{$this->task->title}**")
            ->when($this->task->description, function (MailMessage $message) {
                $message->line($this->task->description);
            })
            ->line($dueText)
            ->action('View Task', "{$appUrl}/tasks");
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $points = $this->task->getEffectivePoints();
        $body = $points > 0
            ? "{$points} pts on the line"
            : 'Reminder from Kinhold';

        return (new WebPushMessage)
            ->title("Due today: {$this->task->title}")
            ->body($body)
            ->icon('/icons/icon-192.png')
            ->badge('/icons/badge-96.png')
            ->tag('task-due-'.$this->task->id)
            ->data([
                'type' => 'task_due_soon',
                'url' => '/tasks?focus='.$this->task->id,
                'task_id' => $this->task->id,
            ])
            ->options(['TTL' => 60 * 60 * 24]);
    }
}
