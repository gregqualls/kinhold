<?php

namespace App\Http\Middleware;

use App\Services\BillingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Server-side paywall gate (#264). Mirrors the SPA's `useBillingGate` check on
 * the API surface so removing the client-side overlay (DevTools) doesn't grant
 * access to feature endpoints.
 *
 * Returns 402 Payment Required when the family's subscription has lapsed; the
 * SPA's Axios interceptor catches this and re-mounts the SubscriptionPaywall.
 *
 * `needs_onboarding` is intentionally allowed through — that state is handled
 * by the SPA redirecting the billing owner to /onboarding. Blocking it at the
 * API would 402 the wizard's own data fetches.
 *
 * Apply via the `billing.required` alias to feature route groups (tasks, vault,
 * calendar, chat, points, rewards, badges, food, etc.). Do NOT apply to auth,
 * billing management, settings (incl. GDPR data export + account deletion),
 * onboarding, or family invite routes.
 */
class BillingRequired
{
    public function __construct(private readonly BillingService $billing) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->billing->isEnabled()) {
            return $next($request);
        }

        $user = $request->user();
        $family = $user?->family;

        if (! $family) {
            return $next($request);
        }

        $reason = $this->billing->paywallReason($family);

        if ($reason === null || $reason === 'needs_onboarding') {
            return $next($request);
        }

        return response()->json([
            'message' => 'Subscription required.',
            'paywall_reason' => $reason,
        ], 402);
    }
}
