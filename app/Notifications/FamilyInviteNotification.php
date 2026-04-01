<?php

namespace App\Notifications;

use App\Models\Family;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FamilyInviteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Family $family,
        public string $inviteCode,
        public string $inviterName,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appUrl = config('app.url');
        $joinUrl = "{$appUrl}/register?invite_code={$this->inviteCode}";

        return (new MailMessage)
            ->subject("You're invited to join {$this->family->name} on Kinhold")
            ->greeting('Hello!')
            ->line("{$this->inviterName} has invited you to join the **{$this->family->name}** family on Kinhold.")
            ->line('Kinhold is a family hub for managing calendars, tasks, important documents, and more — all in one place.')
            ->line("**Your invite code:** `{$this->inviteCode}`")
            ->action('Join the Family', $joinUrl)
            ->line('You can also enter the invite code manually when you register.')
            ->salutation('Welcome to the family!');
    }
}
