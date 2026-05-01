<?php

namespace App\Services;

use App\Models\Family;

/**
 * Read-only billing facade. The 70-A foundation only exposes the on/off gate
 * and a coarse plan resolver — Stripe API calls land in 70-B+ as the surface
 * area grows.
 *
 * Plan tokens returned by resolveCurrentPlan():
 *   - 'self_hosted' — billing globally disabled (BILLING_ENABLED=false)
 *   - 'free'        — billing on, no active subscription
 *   - 'base'        — billing on, base subscription active
 */
final class BillingService
{
    public function isEnabled(): bool
    {
        return (bool) config('kinhold.billing_enabled', false);
    }

    public function resolveCurrentPlan(Family $family): string
    {
        if (! $this->isEnabled()) {
            return 'self_hosted';
        }

        return $family->subscribed('default') ? 'base' : 'free';
    }
}
