<?php

namespace App\Notifications\Billing;

use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Models\Family;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fired by Stripe `customer.subscription.trial_will_end` (3 days before end).
 */
class TrialEndingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Family $family,
        public string $trialEndsAt,
    ) {}

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
            ->subject('Your Kinhold trial ends in 3 days')
            ->view('emails.billing.trial-ending', [
                'subject' => 'Your Kinhold trial ends in 3 days',
                'userName' => $notifiable->name,
                'familyName' => $this->family->name,
                'trialEndsAt' => $this->trialEndsAt,
                'portalUrl' => $portalUrl,
            ]);
    }
}
