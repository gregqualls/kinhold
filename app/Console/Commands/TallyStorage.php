<?php

namespace App\Console\Commands;

use App\Services\StorageMeteringService;
use Illuminate\Console\Command;

/**
 * Nightly storage tally — recompute every family's `total_bytes` and push
 * any unreported overage delta to Stripe as a metered usage event.
 *
 * Idempotent: pushes deltas only, so running twice in a row is a no-op for
 * the second run. Safe to invoke ad-hoc from a console for a forced refresh.
 */
class TallyStorage extends Command
{
    protected $signature = 'kinhold:tally-storage';

    protected $description = 'Recompute per-family storage totals and report overage to Stripe (#216 / 70-C)';

    public function handle(StorageMeteringService $svc): int
    {
        $stats = $svc->tallyAll();

        $this->info("Recalculated {$stats['recalculated']} families, reported {$stats['reported']} to Stripe.");

        return self::SUCCESS;
    }
}
