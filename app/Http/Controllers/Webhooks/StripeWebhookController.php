<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\Family;
use App\Models\WebhookEvent;
use App\Notifications\Billing\LiteTrialEndedNotification;
use App\Notifications\Billing\PaymentFailedNotification;
use App\Notifications\Billing\SubscriptionCancelledNotification;
use App\Notifications\Billing\SubscriptionResumedNotification;
use App\Notifications\Billing\TrialEndingNotification;
use App\Services\BillingService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stripe webhook receiver. Extends Cashier's controller to inherit the built-in
 * subscription-sync handlers and signature middleware, then layers on:
 *
 *   - Idempotency via the `webhook_events` table (Stripe retries until 2xx;
 *     network blips can also dupe).
 *   - Lifecycle notifications to the family's billing owner.
 *   - Grace period bookkeeping on `families.payment_failed_at` — the actual
 *     state machine (day-3 reminder, day-7 downgrade) lives in
 *     `kinhold:enforce-grace-period`.
 *
 * Cashier's own handlers (`customer.subscription.{created,updated,deleted}`)
 * mirror Stripe state into the `subscriptions` table; we override them to
 * `parent::handle...()` first, then dispatch our notifications. See 70-H (#221).
 */
class StripeWebhookController extends CashierWebhookController
{
    public function handleWebhook(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);

        if (! is_array($payload) || ! isset($payload['id'], $payload['type'])) {
            return new Response('Invalid payload', 400);
        }

        // Idempotency: first writer wins. The (provider, event_id) unique
        // constraint serializes concurrent deliveries — the duplicate insert
        // throws and we catch it. Any pre-existing row (whether processed or
        // still in-flight from a parallel worker) means we ack 200 and bail;
        // Stripe's at-least-once semantics expect that, and if the in-flight
        // worker actually crashes, Stripe will redeliver later.
        $eventId = (string) $payload['id'];

        try {
            WebhookEvent::create([
                'provider' => 'stripe',
                'event_id' => $eventId,
            ]);
        } catch (QueryException $e) {
            Log::info('Stripe webhook duplicate ignored', ['event_id' => $eventId, 'type' => $payload['type']]);

            return new Response('Already processed', 200);
        }

        $response = parent::handleWebhook($request);

        WebhookEvent::query()
            ->where('provider', 'stripe')
            ->where('event_id', $eventId)
            ->update(['processed_at' => Carbon::now()]);

        return $response;
    }

    /**
     * Build the SPA URL where the billing owner manages their subscription.
     * Centralized here so all six lifecycle emails (and the controller's own
     * log messages) share one canonical path — rename it once, not 7 times.
     */
    public static function billingPortalReturnUrl(): string
    {
        return rtrim((string) config('app.url'), '/').'/settings/billing';
    }

    /**
     * `checkout.session.completed` fires when the customer finishes Checkout.
     * Cashier doesn't ship a default handler — we use it to persist any
     * onboarding metadata the SPA passed through (the AI tier choice was
     * already written before the redirect, so this is mostly a nudge to make
     * sure the family.stripe_id is set, which Cashier handles via subscription
     * events anyway). Returning success keeps Stripe happy.
     */
    protected function handleCheckoutSessionCompleted(array $payload): Response
    {
        $session = $payload['data']['object'] ?? [];
        $customerId = $session['customer'] ?? null;

        if ($customerId && ! Cashier::findBillable($customerId)) {
            Log::warning('Stripe checkout.session.completed for unknown customer', [
                'customer' => $customerId,
                'session' => $session['id'] ?? null,
            ]);
        }

        return $this->successMethod();
    }

    /**
     * Successful invoice payment — clears any active grace period and, if the
     * family had been downgraded, restores their previous AI tier.
     */
    protected function handleInvoicePaid(array $payload): Response
    {
        $customerId = $payload['data']['object']['customer'] ?? null;

        if (! $customerId) {
            return $this->successMethod();
        }

        /** @var Family|null $family */
        $family = Cashier::findBillable($customerId);

        if (! $family) {
            return $this->successMethod();
        }

        // Read+mutate+write settings under a row lock so a concurrent webhook
        // (e.g., subscription.updated landing in the same millisecond) can't
        // clobber our update of the JSON column.
        [$shouldNotify, $previousTier] = DB::transaction(function () use ($family): array {
            $locked = Family::query()->whereKey($family->getKey())->lockForUpdate()->first();
            if ($locked === null) {
                return [false, null];
            }

            $wasDowngraded = ! empty($locked->settings['downgraded_at']);

            if ($locked->payment_failed_at === null && ! $wasDowngraded) {
                return [false, null];
            }

            $settings = $locked->settings ?? [];
            $previous = $wasDowngraded ? ($settings['ai_plan_before_downgrade'] ?? null) : null;

            unset(
                $settings['downgraded_at'],
                $settings['ai_plan_before_downgrade'],
                $settings['storage_soft_capped'],
                $settings['grace_day_3_sent_at'],
            );

            $locked->forceFill(['settings' => $settings, 'payment_failed_at' => null])->save();

            return [true, $previous];
        });

        if (! $shouldNotify) {
            return $this->successMethod();
        }

        $family->refresh();

        $restoredTier = null;
        if ($previousTier !== null && in_array($previousTier, ['lite', 'standard', 'pro'], true)) {
            try {
                app(BillingService::class)->selectAiTier($family, $previousTier);
                $restoredTier = $previousTier;
            } catch (\Throwable $e) {
                // Stripe call failed — log and let the operator restore manually.
                // We've already cleared local grace flags; the family is
                // back to the free AI tier, which is safer than leaving
                // them flagged as in-grace forever.
                Log::error('Failed to restore AI tier after invoice.paid', [
                    'family_id' => $family->id,
                    'previous_tier' => $previousTier,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->notifyOwner($family, new SubscriptionResumedNotification($family, $restoredTier));

        return $this->successMethod();
    }

    /**
     * Failed invoice payment — sets `payment_failed_at` and fires the day-0
     * notification immediately. Day 3 + 7 emails come from the scheduler.
     */
    protected function handleInvoicePaymentFailed(array $payload): Response
    {
        $customerId = $payload['data']['object']['customer'] ?? null;

        if (! $customerId) {
            return $this->successMethod();
        }

        /** @var Family|null $family */
        $family = Cashier::findBillable($customerId);

        if (! $family) {
            return $this->successMethod();
        }

        // Don't restart the grace period clock if we're already in one — the
        // dunning state machine relies on the original failure timestamp to
        // schedule day-3/day-7 actions correctly. Lock the row so a concurrent
        // webhook can't both decide they're "the first" failure.
        $shouldNotify = DB::transaction(function () use ($family): bool {
            $locked = Family::query()->whereKey($family->getKey())->lockForUpdate()->first();
            if ($locked === null || $locked->payment_failed_at !== null) {
                return false;
            }

            $locked->forceFill(['payment_failed_at' => Carbon::now()])->save();

            return true;
        });

        if ($shouldNotify) {
            $this->notifyOwner($family->fresh(), new PaymentFailedNotification($family->fresh()));
        }

        return $this->successMethod();
    }

    /**
     * Stripe-recommended hook to nudge customers a few days before a trial
     * converts to a paid subscription. Sends the trial-ending email.
     */
    protected function handleCustomerSubscriptionTrialWillEnd(array $payload): Response
    {
        $object = $payload['data']['object'] ?? [];
        $customerId = $object['customer'] ?? null;
        $trialEnd = $object['trial_end'] ?? null;

        if (! $customerId) {
            return $this->successMethod();
        }

        /** @var Family|null $family */
        $family = Cashier::findBillable($customerId);

        if (! $family) {
            return $this->successMethod();
        }

        $trialEndsAt = $trialEnd
            ? Carbon::createFromTimestamp((int) $trialEnd)->toFormattedDateString()
            : 'soon';

        $this->notifyOwner($family, new TrialEndingNotification($family, $trialEndsAt));

        return $this->successMethod();
    }

    /**
     * Override Cashier's deletion handler to layer on the cancellation email
     * after the subscription record is marked canceled.
     */
    protected function handleCustomerSubscriptionDeleted(array $payload): Response
    {
        $response = parent::handleCustomerSubscriptionDeleted($payload);

        $customerId = $payload['data']['object']['customer'] ?? null;
        $deletedStripeId = $payload['data']['object']['id'] ?? null;
        if (! $customerId || ! $deletedStripeId) {
            return $response;
        }

        /** @var Family|null $family */
        $family = Cashier::findBillable($customerId);
        if (! $family) {
            return $response;
        }

        // Only fire the cancellation email when it's the family's *default*
        // subscription that ended — not some side subscription a future feature
        // (add-on, gift, etc.) might attach to the same customer. Today only
        // 'default' exists, so this is forward-defensive.
        $subscription = $family->subscription('default');
        if ($subscription && $subscription->stripe_id === $deletedStripeId) {
            $this->notifyOwner($family, new SubscriptionCancelledNotification($family));
        }

        return $response;
    }

    /**
     * `customer.subscription.updated` fires for many transitions; we only act on
     * trialing → active|past_due (issue #230). When the family auto-converts off
     * trial without ever picking a paid AI tier, the implicit Lite grant from
     * `AiUsageService::planFor()` would silently keep applying — clear the slug
     * (or 'lite' if they explicitly picked it during trial, since that was free
     * too) so the family falls back to the global default plan post-trial.
     * Cashier's parent handler runs first to mirror the Stripe state into our
     * subscriptions table.
     */
    protected function handleCustomerSubscriptionUpdated(array $payload): Response
    {
        $response = parent::handleCustomerSubscriptionUpdated($payload);

        $object = $payload['data']['object'] ?? [];
        $previous = $payload['data']['previous_attributes'] ?? [];
        $customerId = $object['customer'] ?? null;

        $statusFlippedFromTrial = ($previous['status'] ?? null) === 'trialing'
            && in_array($object['status'] ?? null, ['active', 'past_due'], true);

        if (! $statusFlippedFromTrial || ! $customerId) {
            return $response;
        }

        /** @var Family|null $family */
        $family = Cashier::findBillable($customerId);
        if (! $family) {
            return $response;
        }

        $hadExplicitLitePick = DB::transaction(function () use ($family): bool {
            $locked = Family::query()->whereKey($family->getKey())->lockForUpdate()->first();
            if ($locked === null) {
                return false;
            }

            $settings = $locked->settings ?? [];
            $currentPlan = $settings['chatbot']['plan'] ?? null;

            // Only clear the implicit-trial cases. Standard/Pro/BYOK during trial
            // already paid (trial was ended via selectAiTier()), so their settings
            // and Stripe state are in lock-step — don't touch them.
            if ($currentPlan !== null && $currentPlan !== 'lite') {
                return false;
            }

            $chatbot = is_array($settings['chatbot'] ?? null) ? $settings['chatbot'] : [];
            Arr::forget($chatbot, 'plan');

            if (empty($chatbot)) {
                Arr::forget($settings, 'chatbot');
            } else {
                $settings['chatbot'] = $chatbot;
            }

            $locked->forceFill(['settings' => $settings])->save();

            // Email only when the family had explicitly picked Lite — null-plan
            // families never saw a "Lite" UI affordance, so an email about it
            // would confuse more than help.
            return $currentPlan === 'lite';
        });

        if ($hadExplicitLitePick) {
            $this->notifyOwner($family->fresh(), new LiteTrialEndedNotification($family->fresh()));
        }

        return $response;
    }

    /**
     * Send a notification to the family's designated billing owner. No-op if
     * the family hasn't designated one yet — we don't fall back to all parents
     * because that would spam users about a state they can't act on.
     */
    private function notifyOwner(Family $family, object $notification): void
    {
        $owner = $family->billingOwner()->first();

        if ($owner === null) {
            Log::info('Skipping billing notification — no billing owner', [
                'family_id' => $family->id,
                'notification' => get_class($notification),
            ]);

            return;
        }

        $owner->notify($notification);
    }
}
