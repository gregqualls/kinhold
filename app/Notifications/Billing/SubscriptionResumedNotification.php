<?php

namespace App\Notifications\Billing;

use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Models\Family;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fired by the `invoice.paid` webhook handler when a previously downgraded
 * family has its previous AI tier and storage cap restored.
 */
class SubscriptionResumedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Family $family,
        public ?string $restoredTier = null,
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
        $appUrl = (string) config('app.url');
        $portalUrl = StripeWebhookController::billingPortalReturnUrl();

        return (new MailMessage)
            ->subject("You're all set — Kinhold is restored")
            ->view('emails.billing.subscription-resumed', [
                'subject' => "You're all set — Kinhold is restored",
                'userName' => $notifiable->name,
                'familyName' => $this->family->name,
                'restoredTier' => $this->restoredTier,
                'appUrl' => $appUrl,
                'portalUrl' => $portalUrl,
            ]);
    }
}
