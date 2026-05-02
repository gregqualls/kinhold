<?php

namespace App\Services;

use App\Models\Family;
use Illuminate\Http\Exceptions\HttpResponseException;
use Laravel\Cashier\Checkout;
use Laravel\Cashier\PaymentMethod;

/**
 * Billing facade. 70-A landed the gate + plan resolver. 70-B (#215) extends
 * with thin wrappers around Cashier's checkout + customer portal + lifecycle
 * methods. Keeps the controller free of Cashier internals so 70-H (webhooks)
 * can change subscription state without breaking the API surface.
 *
 * Plan tokens returned by resolveCurrentPlan():
 *   - 'self_hosted' — billing globally disabled (BILLING_ENABLED=false). Free
 *                     forever under EL2.
 *   - 'none'        — billing on, no active subscription. Hosted Kinhold has
 *                     no permanent free tier — this is the "trial available"
 *                     or "trial expired, please subscribe" state.
 *   - 'base'        — billing on, base subscription active (incl. trialing).
 */
class BillingService
{
    public function __construct(private readonly StorageMeteringService $storage) {}

    public function isEnabled(): bool
    {
        return (bool) config('kinhold.billing_enabled', false);
    }

    public function resolveCurrentPlan(Family $family): string
    {
        if (! $this->isEnabled()) {
            return 'self_hosted';
        }

        return $family->subscribed('default') ? 'base' : 'none';
    }

    /**
     * Snapshot of the current billing state — what the BillingPanel renders.
     * Returns null payment fields when there's no Stripe customer yet so the
     * SPA can show "no payment method" without needing extra API round-trips.
     */
    public function summary(Family $family): array
    {
        $subscription = $family->subscription('default');
        $paymentMethod = $family->hasStripeId() ? $family->defaultPaymentMethod() : null;
        // defaultPaymentMethod() can return a legacy Stripe\Card or BankAccount
        // for old customers — only modern PaymentMethod objects expose card
        // details. Reach through to the underlying Stripe object so static
        // analysis can see the typed `card` property (Cashier's wrapper
        // delegates via __get).
        $card = $paymentMethod instanceof PaymentMethod
            ? $paymentMethod->asStripePaymentMethod()->card
            : null;

        // A family is trial-eligible if (1) the trial config is non-zero and
        // (2) they've never created a Stripe customer (which Cashier does on
        // first checkout). Returning families re-subscribing don't get a
        // second free trial — Stripe enforces this anyway, but surfacing it
        // up front keeps the SPA's CTA copy honest.
        $trialDays = (int) config('kinhold.billing.trial_days', 0);
        $trialEligible = $trialDays > 0 && ! $family->hasStripeId();

        return [
            'plan' => $this->resolveCurrentPlan($family),
            'status' => $subscription?->stripe_status,
            'on_trial' => $subscription?->onTrial() ?? false,
            'on_grace_period' => $subscription?->onGracePeriod() ?? false,
            'cancelled' => $subscription?->canceled() ?? false,
            'trial_ends_at' => $subscription?->trial_ends_at?->toIso8601String(),
            'ends_at' => $subscription?->ends_at?->toIso8601String(),
            'trial_eligible' => $trialEligible,
            'trial_days' => $trialDays,
            'base_price_cents' => (int) config('kinhold.billing.base_plan.price_monthly_cents', 0),
            'payment_method' => $card ? [
                'brand' => $card->brand,
                'last4' => $card->last4,
            ] : null,
            'storage' => $this->storage->summaryFor($family),
        ];
    }

    /**
     * Build a Stripe Checkout session for the base plan. Returns a Cashier
     * Checkout instance whose `->url` is the redirect target. Throws via
     * HttpResponseException if the price ID isn't configured — that turns
     * what would be a 500 into a clean 422 the SPA can render.
     */
    public function createBaseCheckout(Family $family, string $successUrl, string $cancelUrl): Checkout
    {
        $priceId = config('kinhold.billing.base_plan.stripe_price_id');

        if (empty($priceId)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Base plan is not configured. Contact your administrator.',
            ], 422));
        }

        $trialDays = (int) config('kinhold.billing.trial_days', 0);

        $builder = $family->newSubscription('default', $priceId);

        // Layer the metered storage price onto the same subscription so the
        // first invoice already has the line item we need to push usage to.
        // Acts as the forward-compat path; the nightly tally also lazy-adds
        // the price as a self-healing safety net.
        $storagePriceId = config('kinhold.billing.storage.stripe_price_id');
        if (! empty($storagePriceId) && is_string($storagePriceId)) {
            $builder->meteredPrice($storagePriceId);
        }

        if ($trialDays > 0 && ! $family->hasStripeId()) {
            $builder->trialDays($trialDays);
        }

        return $builder->checkout([
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

    /**
     * URL for the Stripe-hosted Customer Portal — invoices, payment methods,
     * upgrades, cancellations all live there. We never bring that UI in-house.
     */
    public function billingPortalUrl(Family $family, string $returnUrl): string
    {
        return $family->billingPortalUrl($returnUrl);
    }

    /**
     * Cancel at period end — keeps service active until the current invoice
     * cycle finishes, then lapses. Mirrors Stripe's "cancel at period end"
     * checkbox in the dashboard.
     */
    public function cancel(Family $family): void
    {
        $family->subscription('default')?->cancel();
    }

    /**
     * Resume a subscription that was canceled but is still inside its grace
     * period (i.e. the period hasn't ended yet). Once `ends_at` has passed,
     * the user has to re-checkout instead.
     */
    public function resume(Family $family): void
    {
        $family->subscription('default')?->resume();
    }
}
