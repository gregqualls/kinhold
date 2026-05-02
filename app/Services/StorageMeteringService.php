<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Family;
use App\Models\FamilyStorageUsage;
use App\Models\VaultEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Sums Document.size for each Family, persists the total, and pushes overage
 * usage to Stripe for metered billing. Single source of truth — the artisan
 * command, the Document model events, and BillingService::summary() all read
 * through this service.
 *
 * Reporting unit: GB rounded up, base-1024 (1 GiB = 1024^3 bytes). Stripe gets
 * the *delta* in GB since the last report; meter events are additive under
 * `sum` aggregation so absolute pushes would double-count nightly.
 */
class StorageMeteringService
{
    /** 5 GiB included with the base plan (matches `billing.storage.included_gb`). */
    public const INCLUDED_BYTES = 5 * (1024 ** 3);

    public const BYTES_PER_GB = 1024 ** 3;

    /**
     * Polymorphic owner registry: `Document.documentable_type` => column on
     * the owner table that holds `family_id`. Adding Tasks/Recipes/etc.
     * later is a one-line edit when those models gain `morphMany(Document)`.
     *
     * @var array<class-string, string>
     */
    private const OWNERS = [
        VaultEntry::class => 'family_id',
    ];

    /**
     * Recompute total bytes for a family by summing documents.size joined
     * through every registered polymorphic owner. Upserts the
     * `family_storage_usages` row and returns it.
     */
    public function recalcFamily(Family $family): FamilyStorageUsage
    {
        $total = 0;

        foreach (self::OWNERS as $ownerClass => $familyIdColumn) {
            $instance = new $ownerClass;
            $ownerTable = $instance->getTable();
            $ownerKey = $instance->getKeyName();

            $sum = DB::table('documents')
                ->join($ownerTable, 'documents.documentable_id', '=', $ownerTable.'.'.$ownerKey)
                ->where('documents.documentable_type', $ownerClass)
                ->where($ownerTable.'.'.$familyIdColumn, $family->id)
                ->sum('documents.size');

            $total += (int) $sum;
        }

        /** @var FamilyStorageUsage $usage */
        $usage = FamilyStorageUsage::query()->updateOrCreate(
            ['family_id' => $family->id],
            ['total_bytes' => $total, 'last_calculated_at' => now()],
        );

        return $usage;
    }

    /**
     * Real-time hook target. Resolves the family from the document's owner and
     * recalcs synchronously. Cheap (one aggregate per family) and keeps the
     * UI number warm without waiting for the nightly tally.
     *
     * No-ops gracefully if the document's owner isn't in the registry or the
     * owner row was already deleted.
     */
    public function onDocumentChange(Document $document): void
    {
        $family = $this->resolveFamily($document);

        if ($family === null) {
            return;
        }

        try {
            $this->recalcFamily($family);
        } catch (Throwable $e) {
            // Don't break the upload / delete flow if recalc fails — the
            // nightly tally will fix it. Log so we notice in production.
            Log::warning('StorageMeteringService recalc failed', [
                'family_id' => $family->id,
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Push the unreported overage delta to Stripe as a meter event. Returns
     * the freshly-saved usage row whether or not a push happened.
     *
     * Skipped (no Stripe call) when:
     *   - billing is globally disabled
     *   - the family has no active subscription
     *   - the metered storage price isn't configured
     *   - the new overage is ≤ already-reported overage (deltas only)
     *
     * Stripe's `sum` aggregation handles billing-period rollover automatically,
     * so we never need to "reset" — we just track our own absolute high-water
     * mark and push the difference.
     */
    public function reportToStripe(Family $family): FamilyStorageUsage
    {
        $usage = $this->recalcFamily($family);

        if (! (bool) config('kinhold.billing_enabled', false)) {
            return $usage;
        }

        $priceId = config('kinhold.billing.storage.stripe_price_id');
        if (empty($priceId) || ! is_string($priceId)) {
            return $usage;
        }

        $subscription = $family->subscription('default');
        if ($subscription === null) {
            return $usage;
        }

        $newOverageGb = $this->bytesToOverageGb($usage->total_bytes);
        $reportedOverageGb = $this->bytesToOverageGb($usage->reported_bytes);
        $delta = $newOverageGb - $reportedOverageGb;

        if ($delta <= 0) {
            return $usage;
        }

        try {
            if (! $subscription->hasPrice($priceId)) {
                $subscription->addMeteredPrice($priceId);
                $subscription->refresh();
            }

            $item = $subscription->findItemOrFail($priceId);
            $item->reportUsage($delta);

            $usage->forceFill([
                'reported_bytes' => $usage->total_bytes,
                'last_reported_at' => now(),
            ])->save();
        } catch (Throwable $e) {
            Log::warning('StorageMeteringService Stripe push failed', [
                'family_id' => $family->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $usage;
    }

    /**
     * Recompute every family and push deltas to Stripe. Returns counts for
     * the artisan command's success line.
     *
     * @return array{recalculated: int, reported: int}
     */
    public function tallyAll(): array
    {
        $recalculated = 0;
        $reported = 0;

        Family::query()->lazyById(100)->each(function (Family $family) use (&$recalculated, &$reported): void {
            $before = (int) FamilyStorageUsage::query()
                ->where('family_id', $family->id)
                ->value('reported_bytes');

            $usage = $this->reportToStripe($family);
            $recalculated++;

            if ($usage->reported_bytes > $before) {
                $reported++;
            }
        });

        return ['recalculated' => $recalculated, 'reported' => $reported];
    }

    /**
     * Snapshot for `BillingService::summary()` — what the BillingPanel reads.
     *
     * @return array{
     *     used_bytes: int,
     *     included_bytes: int,
     *     overage_gb: int,
     *     overage_cents: int,
     *     over_limit: bool,
     *     last_calculated_at: ?string,
     * }
     */
    public function summaryFor(Family $family): array
    {
        /** @var FamilyStorageUsage|null $usage */
        $usage = FamilyStorageUsage::query()->where('family_id', $family->id)->first();
        $totalBytes = (int) ($usage?->total_bytes ?? 0);
        $overageGb = $this->bytesToOverageGb($totalBytes);
        $centsPerGb = (int) config('kinhold.billing.storage.overage_cents_per_gb', 100);

        return [
            'used_bytes' => $totalBytes,
            'included_bytes' => self::INCLUDED_BYTES,
            'overage_gb' => $overageGb,
            'overage_cents' => $overageGb * $centsPerGb,
            'over_limit' => $totalBytes > self::INCLUDED_BYTES,
            'last_calculated_at' => $usage?->last_calculated_at?->toIso8601String(),
        ];
    }

    /**
     * `ceil((bytes - included) / 1 GiB)`, floored at 0. Public for tests.
     */
    public function bytesToOverageGb(int $bytes): int
    {
        if ($bytes <= self::INCLUDED_BYTES) {
            return 0;
        }

        return (int) ceil(($bytes - self::INCLUDED_BYTES) / self::BYTES_PER_GB);
    }

    /**
     * Walk the polymorphic registry to find the family for a document.
     */
    private function resolveFamily(Document $document): ?Family
    {
        $ownerClass = $document->documentable_type;
        if (! isset(self::OWNERS[$ownerClass])) {
            return null;
        }

        $familyIdColumn = self::OWNERS[$ownerClass];

        $instance = new $ownerClass;
        $familyId = DB::table($instance->getTable())
            ->where($instance->getKeyName(), $document->documentable_id)
            ->value($familyIdColumn);

        if ($familyId === null) {
            return null;
        }

        return Family::query()->find($familyId);
    }
}
