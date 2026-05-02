<?php

namespace App\Notifications\Billing;

use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Models\Family;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fired on day 7 of the grace period when `kinhold:enforce-grace-period`
 * removes the AI tier and applies the soft storage cap.
 */
class SubscriptionDowngradedNotification extends Notification implements ShouldQueue
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
            ->subject('Your Kinhold AI features have been paused')
            ->view('emails.billing.subscription-downgraded', [
                'subject' => 'Your Kinhold AI features have been paused',
                'userName' => $notifiable->name,
                'familyName' => $this->family->name,
                'portalUrl' => $portalUrl,
            ]);
    }
}
