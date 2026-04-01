<?php

namespace App\Notifications;

use App\Models\Family;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Family $family,
        public bool $isNewFamily = false,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Always send welcome emails (no preference check)
        if (! $notifiable->email) {
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

        $message = (new MailMessage)
            ->subject('Welcome to Kinhold!')
            ->greeting("Welcome, {$notifiable->name}!")
            ->line("You've successfully joined **{$this->family->name}** on Kinhold.");

        if ($this->isNewFamily) {
            $message->line("As the family creator, you have full access to manage your family hub. Here's what you can do:");
        } else {
            $message->line("Here's what you can do:");
        }

        $message
            ->line('- **Calendar**: View all your family events in one place')
            ->line('- **Tasks**: Create and assign tasks, earn points for completing them')
            ->line('- **Vault**: Securely store important family documents and information')
            ->line('- **Hub AI**: Ask questions about your family data')
            ->action('Get Started', $appUrl);

        if ($this->isNewFamily) {
            $message->line('Invite your family members from Settings to get everyone on board!');
        }

        return $message;
    }
}
