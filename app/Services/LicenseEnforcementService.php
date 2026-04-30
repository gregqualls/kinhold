<?php

namespace App\Services;

use App\Models\Family;

/**
 * Soft-enforces the Elastic License "Kinhold Single-Family Addendum" on
 * self-hosted instances. Honor system at the code level — see LICENSE for the
 * actual contract. The banner + log lines exist to ensure operators are
 * *informed* about the limit, not to technically prevent its violation.
 *
 * Hosted Kinhold (kinhold.app) is unaffected: shouldWarn() short-circuits when
 * self_hosted is false.
 */
class LicenseEnforcementService
{
    private ?int $cachedFamilyCount = null;

    /**
     * Whether the SPA should render the single-family warning banner.
     *
     * True only when (1) we're running self-hosted, (2) the operator has not
     * acknowledged a commercial license via env, and (3) more than one family
     * row exists. Hosted instances always return false.
     */
    public function shouldWarn(): bool
    {
        if (! (bool) config('kinhold.self_hosted', false)) {
            return false;
        }

        if ($this->acknowledged()) {
            return false;
        }

        return $this->familyCount() > 1;
    }

    /**
     * Cached per-instance to avoid re-querying within a single request. Reset
     * naturally between requests since the service is request-scoped.
     */
    public function familyCount(): int
    {
        return $this->cachedFamilyCount ??= Family::count();
    }

    public function acknowledged(): bool
    {
        return (bool) config('kinhold.commercial_license_acknowledged', false);
    }
}
