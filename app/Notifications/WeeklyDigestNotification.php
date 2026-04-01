<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyDigestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public array $digest,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        if (! $notifiable->wantsEmail('email_weekly_digest')) {
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
        $familyName = $this->digest['family_name'] ?? 'Your Family';
        $weekRange = $this->digest['week_range'] ?? 'This Week';

        $message = (new MailMessage)
            ->subject("Weekly Digest for {$familyName}")
            ->greeting("Hi {$notifiable->name}!")
            ->line("Here's your weekly summary for **{$familyName}** ({$weekRange}):");

        // Tasks summary
        $tasksCompleted = $this->digest['tasks_completed'] ?? 0;
        $tasksPending = $this->digest['tasks_pending'] ?? 0;
        $tasksOverdue = $this->digest['tasks_overdue'] ?? 0;

        $message->line('**Tasks**');
        $message->line("- {$tasksCompleted} completed this week");
        $message->line("- {$tasksPending} still pending");

        if ($tasksOverdue > 0) {
            $message->line("- {$tasksOverdue} overdue");
        }

        // Points summary
        $pointsEarned = $this->digest['points_earned'] ?? 0;
        $pointsBank = $this->digest['points_bank'] ?? 0;

        if ($pointsEarned > 0 || $pointsBank > 0) {
            $message->line('**Points**');
            $message->line("- {$pointsEarned} points earned this week");
            $message->line("- {$pointsBank} total in your bank");
        }

        // Upcoming tasks
        $upcomingTasks = $this->digest['upcoming_tasks'] ?? [];
        if (count($upcomingTasks) > 0) {
            $message->line('**Coming Up Next Week**');
            foreach (array_slice($upcomingTasks, 0, 5) as $task) {
                $dueDate = $task['due_date'] ?? '';
                $message->line("- {$task['title']}".($dueDate ? " (due {$dueDate})" : ''));
            }
        }

        // Badges earned
        $badgesEarned = $this->digest['badges_earned'] ?? [];
        if (count($badgesEarned) > 0) {
            $message->line('**Badges Earned**');
            foreach ($badgesEarned as $badge) {
                $message->line("- {$badge['name']}: {$badge['description']}");
            }
        }

        $message
            ->action('Open Kinhold', $appUrl)
            ->line('Have a great week!');

        return $message;
    }
}
