<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task,
        public User $assignedBy,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        if (!$notifiable->wantsEmail('email_task_assigned')) {
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
}
