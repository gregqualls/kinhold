<?php

namespace App\Notifications\Billing;

use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Models\Family;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fired by `customer.subscription.updated` (trialing → active|past_due) when
 * the family's AI tier was the implicit/explicit Lite-during-trial grant —
 * i.e., they were never billed for AI. The webhook clears `chatbot.plan`, so
 * limits drop to the global default. This email tells them why.
 */
class LiteTrialEndedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Family $family) {}

    public function via(object $notifiable): array
    {
        if (! $notifiable->wantsEmail('billing')) {
            return [];
        }

        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $portalUrl = StripeWebhookController::billingPortalReturnUrl();

        return (new MailMessage)
            ->subject('Your AI Lite trial has ended')
            ->view('emails.billing.lite-trial-ended', [
                'subject' => 'Your AI Lite trial has ended',
                'userName' => $notifiable->name,
                'familyName' => $this->family->name,
                'portalUrl' => $portalUrl,
            ]);
    }
}
