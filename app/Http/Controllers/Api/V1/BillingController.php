<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Exception\ApiErrorException;

/**
 * Billing surface for the family's billing owner. Every action requires three
 * things to be true:
 *
 *   1. BILLING_ENABLED=true (gate is closed for self-hosted + during pre-launch)
 *   2. The user is the family's billing_owner (not just any parent)
 *   3. The user is authenticated (Sanctum middleware)
 *
 * The first two checks live in `authorizeBilling()` so each endpoint reads
 * cleanly. We return 403 (not 404) on failure so the SPA can distinguish
 * "you can't see this" from "this doesn't exist."
 */
class BillingController extends Controller
{
    public function __construct(private readonly BillingService $billing) {}

    /**
     * Snapshot of the current billing state — plan, payment method, lifecycle
     * timestamps. Drives the BillingPanel's main display.
     */
    public function current(Request $request): JsonResponse
    {
        $this->authorizeBilling($request);

        return response()->json($this->billing->summary($request->user()->family));
    }

    /**
     * Create a Stripe Checkout session for the base plan. The SPA hits this,
     * receives a `url`, and does `window.location = url`. Stripe handles the
     * payment form + redirects back to our success/cancel URLs.
     */
    public function checkoutSession(Request $request): JsonResponse
    {
        $this->authorizeBilling($request);

        $validated = $request->validate([
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
            'ai_tier' => 'nullable|string|in:off,byok,lite,standard,pro',
        ]);

        try {
            $checkout = $this->billing->createBaseCheckout(
                $request->user()->family,
                $validated['success_url'],
                $validated['cancel_url'],
                $validated['ai_tier'] ?? null,
            );
        } catch (ApiErrorException $e) {
            return response()->json([
                'message' => 'Could not start checkout. '.$e->getMessage(),
            ], 422);
        }

        // Cashier's Checkout exposes session fields via __get; reach through
        // to the underlying Stripe object so PHPStan can resolve the type.
        return response()->json(['url' => $checkout->asStripeCheckoutSession()->url]);
    }

    /**
     * Create a Stripe Customer Portal session for managing payment methods,
     * viewing invoices, and changing/cancelling the subscription. The portal
     * itself is hosted by Stripe — we just hand out the URL.
     */
    public function portalSession(Request $request): JsonResponse
    {
        $this->authorizeBilling($request);

        $validated = $request->validate([
            'return_url' => 'required|url',
        ]);

        $family = $request->user()->family;

        if (! $family->hasStripeId()) {
            return response()->json([
                'message' => 'No active subscription. Start a checkout first.',
            ], 422);
        }

        try {
            $url = $this->billing->billingPortalUrl($family, $validated['return_url']);
        } catch (ApiErrorException $e) {
            return response()->json([
                'message' => 'Could not open billing portal. '.$e->getMessage(),
            ], 422);
        }

        return response()->json(['url' => $url]);
    }

    /**
     * Cancel the base subscription at the end of the current billing period.
     * The subscription remains active until `ends_at`, then lapses. The user
     * can `resume()` any time before `ends_at`.
     */
    public function cancel(Request $request): JsonResponse
    {
        $this->authorizeBilling($request);

        $family = $request->user()->family;

        if (! $family->subscribed('default')) {
            return response()->json([
                'message' => 'No active subscription to cancel.',
            ], 422);
        }

        $this->billing->cancel($family);

        return response()->json($this->billing->summary($family));
    }

    /**
     * Reverse a cancellation that's still in its grace period (cancelled but
     * not yet expired). Once `ends_at` passes, the user has to re-checkout.
     */
    public function resume(Request $request): JsonResponse
    {
        $this->authorizeBilling($request);

        $family = $request->user()->family;
        $subscription = $family->subscription('default');

        if (! $subscription || ! $subscription->onGracePeriod()) {
            return response()->json([
                'message' => 'No cancelled subscription to resume.',
            ], 422);
        }

        $this->billing->resume($family);

        return response()->json($this->billing->summary($family));
    }

    /**
     * Set the family's AI tier (Off / BYOK / Lite / Standard / Pro). Adds,
     * swaps, or removes a Stripe subscription item and writes the matching
     * settings.* keys atomically — see BillingService::selectAiTier(). Returns
     * the fresh summary so the SPA can update without a second round-trip.
     */
    public function selectAiTier(Request $request): JsonResponse
    {
        $this->authorizeBilling($request);

        $validated = $request->validate([
            'tier' => 'required|string|in:off,byok,lite,standard,pro',
        ]);

        $family = $request->user()->family;

        try {
            $this->billing->selectAiTier($family, $validated['tier']);
        } catch (ApiErrorException $e) {
            return response()->json([
                'message' => 'Could not update AI tier. '.$e->getMessage(),
            ], 422);
        }

        return response()->json($this->billing->summary($family));
    }

    /**
     * Single guard used by every endpoint. Three things have to be true:
     * billing is enabled, the user has a family, and the user IS the family's
     * billing owner. Anything else → 403.
     */
    private function authorizeBilling(Request $request): void
    {
        if (! $this->billing->isEnabled()) {
            abort(403, 'Billing is not enabled.');
        }

        $user = $request->user();
        $family = $user->family;

        if (! $family) {
            abort(403, 'No family on this account.');
        }

        if ($family->billing_owner_id !== $user->id) {
            abort(403, 'Only the family billing owner can manage billing.');
        }
    }
}
