<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task,
        public User $completedBy,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        if (!$notifiable->wantsEmail('email_task_completed')) {
            return [];
        }

        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appUrl = config('app.url');
        $pointsText = $this->task->getEffectivePoints() > 0
            ? " (+{$this->task->getEffectivePoints()} points)"
            : '';

        return (new MailMessage)
            ->subject("Task completed: {$this->task->title}")
            ->greeting("Hi {$notifiable->name}!")
            ->line("**{$this->completedBy->name}** completed a task{$pointsText}:")
            ->line("**{$this->task->title}**")
            ->when($this->task->description, function (MailMessage $message) {
                $message->line($this->task->description);
            })
            ->action('View Tasks', "{$appUrl}/tasks")
            ->line('Keep up the great work!');
    }
}
