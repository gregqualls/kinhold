<?php

namespace App\Services;

use App\Models\Family;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Laravel\Cashier\Checkout;
use Laravel\Cashier\PaymentMethod;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;

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
    /** Tiers a billing owner can select via the AI tier endpoint. `free` is
     *  the default-state slug and is intentionally not selectable here — picking
     *  "Off" routes through `'off'` and clears the slug entirely. */
    private const SELECTABLE_TIERS = ['off', 'byok', 'lite', 'standard', 'pro'];

    public function __construct(
        private readonly StorageMeteringService $storage,
        private readonly AiUsageService $usage,
    ) {}

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
            'ai_tier' => $this->aiTierSummary($family),
        ];
    }

    /**
     * Snapshot of the family's AI tier state + the catalogue of selectable
     * managed tiers (data-driven so the SPA picker can render whichever tiers
     * are configured). The `usage` block is the same payload `/api/v1/chat`
     * already returns, so the picker can show "X / Y messages today" without
     * a second round-trip.
     *
     * @return array<string, mixed>
     */
    public function aiTierSummary(Family $family): array
    {
        $settings = $family->settings ?? [];

        return [
            'mode' => $settings['ai_mode'] ?? 'kinhold',
            'plan' => $settings['chatbot']['plan'] ?? null,
            'usage' => $this->usage->payloadFor($family),
            'tiers' => collect(config('kinhold.chatbot.plans', []))
                ->filter(fn ($p) => (bool) ($p['public'] ?? false))
                ->map(fn ($p, $slug) => [
                    'slug' => $slug,
                    'name' => $p['name'] ?? ucfirst($slug),
                    'daily_messages' => (int) ($p['daily_messages'] ?? 0),
                    'price_cents' => (int) ($p['price_monthly_cents'] ?? 0),
                    'configured' => ! empty($p['stripe_price_id']),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * Build a Stripe Checkout session for the base plan. Returns a Cashier
     * Checkout instance whose `->url` is the redirect target. Throws via
     * HttpResponseException if the price ID isn't configured — that turns
     * what would be a 500 into a clean 422 the SPA can render.
     *
     * `$aiTier` is the onboarding picker's pre-selection. If set, it must be
     * one of SELECTABLE_TIERS — managed tiers (lite/standard/pro) ride along
     * as a third line item so the first invoice already reflects the family's
     * choice. The matching `families.settings` keys are written *after* Stripe
     * accepts the session so a Cashier failure doesn't leave us pointing at a
     * tier that isn't billed (mirrors the no-transaction approach in
     * selectAiTier — see lines 207–211).
     */
    public function createBaseCheckout(Family $family, string $successUrl, string $cancelUrl, ?string $aiTier = null): Checkout
    {
        $priceId = config('kinhold.billing.base_plan.stripe_price_id');

        if (empty($priceId)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Base plan is not configured. Contact your administrator.',
            ], 422));
        }

        if ($aiTier !== null && ! in_array($aiTier, self::SELECTABLE_TIERS, true)) {
            $this->fail('Unknown AI tier.');
        }

        $plans = config('kinhold.chatbot.plans', []);
        $aiPriceId = null;
        if (in_array($aiTier, ['lite', 'standard', 'pro'], true)) {
            $row = $plans[$aiTier] ?? null;
            if (! $row || empty($row['public']) || empty($row['stripe_price_id'])) {
                $this->fail('That AI tier is not available yet.');
            }
            $aiPriceId = (string) $row['stripe_price_id'];
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

        if ($aiPriceId !== null) {
            $builder->price($aiPriceId);
        }

        if ($trialDays > 0 && ! $family->hasStripeId()) {
            $builder->trialDays($trialDays);
        }

        $checkout = $builder->checkout([
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        if ($aiTier !== null) {
            $this->writeAiTierSettings($family, $aiTier);
        }

        return $checkout;
    }

    /**
     * Persist `families.settings` keys for the chosen AI tier. Extracted from
     * selectAiTier() so onboarding's pre-checkout pick lands the same shape.
     * No Stripe side-effect — caller is responsible for billing reconciliation.
     */
    private function writeAiTierSettings(Family $family, string $tier): void
    {
        $settings = $family->settings ?? [];
        $chatbot = is_array($settings['chatbot'] ?? null) ? $settings['chatbot'] : [];

        switch ($tier) {
            case 'byok':
                $settings['ai_mode'] = 'byok';
                Arr::forget($chatbot, 'plan');
                break;

            case 'off':
                $settings['ai_mode'] = 'kinhold';
                Arr::forget($settings, 'ai_api_key');
                Arr::forget($chatbot, 'plan');
                break;

            case 'lite':
            case 'standard':
            case 'pro':
                $settings['ai_mode'] = 'kinhold';
                Arr::forget($settings, 'ai_api_key');
                $chatbot['plan'] = $tier;
                break;
        }

        if (empty($chatbot)) {
            Arr::forget($settings, 'chatbot');
        } else {
            $settings['chatbot'] = $chatbot;
        }

        $family->forceFill(['settings' => $settings])->save();
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

    /**
     * Set the family's AI tier. Coordinates two stores that have to stay in
     * lock-step: a Stripe subscription item (the bill) and `families.settings`
     * (the keys `AiUsageService::planFor()` and `AgentService::resolveApiKey()`
     * read to enforce caps and route API calls).
     *
     * Tier values: 'off' | 'byok' | 'lite' | 'standard' | 'pro'.
     *
     * Stripe call goes first; settings persist only on success. No DB
     * transaction is used — a transaction can't roll back the Stripe side
     * anyway, and wrapping a network call would hold a connection open for
     * the entire Stripe round-trip. The single settings write is naturally
     * atomic at the DB level.
     */
    public function selectAiTier(Family $family, string $tier): void
    {
        if (! $this->isEnabled()) {
            $this->fail('Billing is not enabled.', 403);
        }

        if (! in_array($tier, self::SELECTABLE_TIERS, true)) {
            $this->fail('Unknown AI tier.');
        }

        $subscription = $family->subscription('default');

        if (! $subscription || ! $family->subscribed('default')) {
            $this->fail('Subscribe to the base plan before adding an AI tier.');
        }

        $plans = config('kinhold.chatbot.plans', []);

        if (in_array($tier, ['lite', 'standard', 'pro'], true)) {
            $row = $plans[$tier] ?? null;
            if (! $row || empty($row['public']) || empty($row['stripe_price_id'])) {
                $this->fail('That AI tier is not available yet.');
            }
        }

        $aiPriceIds = $this->configuredAiPriceIds();
        $currentAiPriceId = $this->currentAiPriceId($subscription, $aiPriceIds);
        $newAiPriceId = in_array($tier, ['lite', 'standard', 'pro'], true)
            ? (string) $plans[$tier]['stripe_price_id']
            : null;

        // 1. Reconcile Stripe first — if it throws, settings stay untouched.
        if ($currentAiPriceId !== $newAiPriceId) {
            if ($currentAiPriceId !== null && $newAiPriceId !== null) {
                // Tier → tier: swap with proration.
                $subscription->swap($this->buildSwapPriceList($subscription, $newAiPriceId));
            } elseif ($newAiPriceId !== null) {
                // None → managed: add the new item and invoice the prorated amount now.
                $subscription->addPriceAndInvoice($newAiPriceId);
            } elseif ($currentAiPriceId !== null) {
                // Managed → off/byok: drop the AI item.
                $subscription->removePrice($currentAiPriceId);
            }
        }

        // 2. Persist settings (after Stripe success).
        $settings = $family->settings ?? [];
        $chatbot = is_array($settings['chatbot'] ?? null) ? $settings['chatbot'] : [];

        switch ($tier) {
            case 'byok':
                $settings['ai_mode'] = 'byok';
                Arr::forget($chatbot, 'plan');
                break;

            case 'off':
                $settings['ai_mode'] = 'kinhold';
                Arr::forget($settings, 'ai_api_key');
                Arr::forget($chatbot, 'plan');
                break;

            case 'lite':
            case 'standard':
            case 'pro':
                $settings['ai_mode'] = 'kinhold';
                Arr::forget($settings, 'ai_api_key');
                $chatbot['plan'] = $tier;
                break;
        }

        if (empty($chatbot)) {
            Arr::forget($settings, 'chatbot');
        } else {
            $settings['chatbot'] = $chatbot;
        }

        $family->forceFill(['settings' => $settings])->save();
    }

    /**
     * Find the AI subscription item (if any) currently on the subscription, by
     * matching against the configured AI price IDs. Returns the price ID, not
     * the item ID — the caller wants to know "which tier is active right now".
     *
     * @param  array<int, string>  $aiPriceIds
     */
    private function currentAiPriceId(Subscription $subscription, array $aiPriceIds): ?string
    {
        if (empty($aiPriceIds)) {
            return null;
        }

        foreach ($subscription->items as $item) {
            /** @var SubscriptionItem $item */
            $priceId = $item->stripe_price;
            if (in_array($priceId, $aiPriceIds, true)) {
                return $priceId;
            }
        }

        return null;
    }

    /**
     * Build the price list for `Subscription::swap()` when changing managed
     * tiers. Cashier's swap replaces the item set wholesale, so we have to
     * include every non-AI price (base + storage + anything else) plus the
     * new AI price. Storage stays metered-aware via Cashier's existing item
     * detection.
     *
     * @return array<int, string>
     */
    private function buildSwapPriceList(Subscription $subscription, string $newAiPriceId): array
    {
        $aiPriceIds = $this->configuredAiPriceIds();
        $prices = [];

        foreach ($subscription->items as $item) {
            /** @var SubscriptionItem $item */
            $priceId = $item->stripe_price;
            if (in_array($priceId, $aiPriceIds, true)) {
                continue; // drop the old AI item
            }
            $prices[] = $priceId;
        }

        $prices[] = $newAiPriceId;

        return array_values(array_unique($prices));
    }

    /**
     * @return array<int, string>
     */
    private function configuredAiPriceIds(): array
    {
        return collect(config('kinhold.chatbot.plans', []))
            ->pluck('stripe_price_id')
            ->filter(fn ($id) => is_string($id) && $id !== '')
            ->values()
            ->all();
    }

    /**
     * @return never
     */
    private function fail(string $message, int $status = 422): void
    {
        throw new HttpResponseException(response()->json(['message' => $message], $status));
    }
}
