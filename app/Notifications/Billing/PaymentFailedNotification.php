<?php

namespace App\Notifications\Billing;

use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Models\Family;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fired immediately by the `invoice.payment_failed` webhook handler.
 * Day-0 of the 7-day grace period. Subsequent reminders come from the
 * `kinhold:enforce-grace-period` scheduled command.
 */
class PaymentFailedNotification extends Notification implements ShouldQueue
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
            ->subject("We couldn't process your Kinhold payment")
            ->view('emails.billing.payment-failed', [
                'subject' => "We couldn't process your Kinhold payment",
                'userName' => $notifiable->name,
                'familyName' => $this->family->name,
                'portalUrl' => $portalUrl,
            ]);
    }
}
