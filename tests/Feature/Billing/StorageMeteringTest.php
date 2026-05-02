<?php

namespace Tests\Feature\Billing;

use App\Models\Document;
use App\Models\Family;
use App\Models\FamilyStorageUsage;
use App\Models\User;
use App\Models\VaultCategory;
use App\Models\VaultEntry;
use App\Services\StorageMeteringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorageMeteringTest extends TestCase
{
    use RefreshDatabase;

    private const KIB = 1024;

    private const MIB = 1024 * 1024;

    private const GIB = 1024 * 1024 * 1024;

    /** Family + parent + a vault category to hang documents off of. */
    private function familySetup(): array
    {
        $family = Family::factory()->create();
        $owner = User::factory()->parent()->create(['family_id' => $family->id]);
        $category = $this->vaultCategory($family);

        return [$family, $owner, $category];
    }

    private function vaultCategory(Family $family): VaultCategory
    {
        return VaultCategory::query()->create([
            'id' => (string) Str::uuid(),
            'family_id' => $family->id,
            'name' => 'Test',
            'slug' => 'test-'.Str::lower(Str::random(6)),
            'sort_order' => 0,
        ]);
    }

    private function vaultEntry(Family $family, VaultCategory $category, User $owner): VaultEntry
    {
        return VaultEntry::query()->create([
            'family_id' => $family->id,
            'vault_category_id' => $category->id,
            'created_by' => $owner->id,
            'title' => 'Entry '.Str::random(4),
        ]);
    }

    private function attachDocument(VaultEntry $entry, User $owner, int $bytes): Document
    {
        return Document::query()->create([
            'documentable_type' => VaultEntry::class,
            'documentable_id' => $entry->id,
            'uploaded_by' => $owner->id,
            'original_filename' => 'doc.pdf',
            'stored_filename' => Str::random(8).'.pdf',
            'mime_type' => 'application/pdf',
            'size' => $bytes,
            'disk' => 'local',
            'path' => 'docs/'.Str::random(8).'.pdf',
            'encrypted' => false,
        ]);
    }

    public function test_recalc_sums_documents_for_family_via_vault_entries(): void
    {
        [$family, $owner, $category] = $this->familySetup();
        $entry1 = $this->vaultEntry($family, $category, $owner);
        $entry2 = $this->vaultEntry($family, $category, $owner);

        $this->attachDocument($entry1, $owner, 100 * self::MIB);
        $this->attachDocument($entry1, $owner, 50 * self::MIB);
        $this->attachDocument($entry2, $owner, 25 * self::MIB);

        $usage = app(StorageMeteringService::class)->recalcFamily($family);

        $this->assertSame(175 * self::MIB, $usage->total_bytes);
        $this->assertNotNull($usage->last_calculated_at);
    }

    public function test_recalc_does_not_bleed_across_families(): void
    {
        [$familyA, $ownerA, $categoryA] = $this->familySetup();
        [$familyB, $ownerB, $categoryB] = $this->familySetup();

        $entryA = $this->vaultEntry($familyA, $categoryA, $ownerA);
        $entryB = $this->vaultEntry($familyB, $categoryB, $ownerB);

        $this->attachDocument($entryA, $ownerA, 200 * self::MIB);
        $this->attachDocument($entryB, $ownerB, 99 * self::MIB);

        $svc = app(StorageMeteringService::class);
        $a = $svc->recalcFamily($familyA);
        $b = $svc->recalcFamily($familyB);

        $this->assertSame(200 * self::MIB, $a->total_bytes);
        $this->assertSame(99 * self::MIB, $b->total_bytes);
    }

    public function test_document_created_event_keeps_usage_warm_in_real_time(): void
    {
        [$family, $owner, $category] = $this->familySetup();
        $entry = $this->vaultEntry($family, $category, $owner);

        $doc = $this->attachDocument($entry, $owner, 10 * self::MIB);

        $usage = FamilyStorageUsage::query()->where('family_id', $family->id)->first();
        $this->assertNotNull($usage);
        $this->assertSame(10 * self::MIB, $usage->total_bytes);

        $doc->delete();

        $usage->refresh();
        $this->assertSame(0, $usage->total_bytes);
    }

    public function test_document_change_for_unregistered_owner_type_is_ignored(): void
    {
        // A document attached to something other than a registered owner
        // (here we forge a documentable_type) must not crash and must not
        // create a usage row for any family.
        [$family, $owner] = $this->familySetup();

        Document::query()->create([
            'documentable_type' => 'App\\Models\\NotARealOwner',
            'documentable_id' => $family->id,
            'uploaded_by' => $owner->id,
            'original_filename' => 'orphan.pdf',
            'stored_filename' => 'orphan.pdf',
            'mime_type' => 'application/pdf',
            'size' => 999,
            'disk' => 'local',
            'path' => 'orphan.pdf',
            'encrypted' => false,
        ]);

        $this->assertSame(0, FamilyStorageUsage::query()->count());
    }

    public function test_overage_math_rounds_up_full_gib_base_1024(): void
    {
        $svc = app(StorageMeteringService::class);

        $this->assertSame(0, $svc->bytesToOverageGb(0));
        $this->assertSame(0, $svc->bytesToOverageGb(5 * self::GIB));
        $this->assertSame(1, $svc->bytesToOverageGb(5 * self::GIB + 1));
        $this->assertSame(1, $svc->bytesToOverageGb(6 * self::GIB));
        $this->assertSame(2, $svc->bytesToOverageGb(6 * self::GIB + 1));
        $this->assertSame(3, $svc->bytesToOverageGb(8 * self::GIB - 1));
        $this->assertSame(3, $svc->bytesToOverageGb(8 * self::GIB));
    }

    public function test_summary_for_returns_zero_state_when_no_documents(): void
    {
        [$family] = $this->familySetup();

        $summary = app(StorageMeteringService::class)->summaryFor($family);

        $this->assertSame(0, $summary['used_bytes']);
        $this->assertSame(5 * self::GIB, $summary['included_bytes']);
        $this->assertSame(0, $summary['overage_gb']);
        $this->assertSame(0, $summary['overage_cents']);
        $this->assertFalse($summary['over_limit']);
        $this->assertNull($summary['last_calculated_at']);
    }

    public function test_summary_for_reports_overage_after_recalc(): void
    {
        [$family, $owner, $category] = $this->familySetup();
        $entry = $this->vaultEntry($family, $category, $owner);
        // 5 GiB + 1 byte → overage of 1 GB ($1.00).
        $this->attachDocument($entry, $owner, 5 * self::GIB + 1);

        $summary = app(StorageMeteringService::class)->summaryFor($family);

        $this->assertSame(5 * self::GIB + 1, $summary['used_bytes']);
        $this->assertSame(1, $summary['overage_gb']);
        $this->assertSame(100, $summary['overage_cents']);
        $this->assertTrue($summary['over_limit']);
        $this->assertNotNull($summary['last_calculated_at']);
    }

    public function test_report_to_stripe_skips_when_billing_disabled(): void
    {
        config()->set('kinhold.billing_enabled', false);

        [$family, $owner, $category] = $this->familySetup();
        $entry = $this->vaultEntry($family, $category, $owner);
        $this->attachDocument($entry, $owner, 10 * self::GIB);

        $usage = app(StorageMeteringService::class)->reportToStripe($family);

        // Local row updates either way — only the Stripe push is gated.
        $this->assertSame(10 * self::GIB, $usage->total_bytes);
        $this->assertSame(0, $usage->reported_bytes);
        $this->assertNull($usage->last_reported_at);
    }

    public function test_report_to_stripe_skips_when_family_has_no_subscription(): void
    {
        config()->set('kinhold.billing_enabled', true);
        config()->set('kinhold.billing.storage.stripe_price_id', 'price_test_storage');

        [$family, $owner, $category] = $this->familySetup();
        $entry = $this->vaultEntry($family, $category, $owner);
        $this->attachDocument($entry, $owner, 6 * self::GIB);

        $usage = app(StorageMeteringService::class)->reportToStripe($family);

        $this->assertSame(6 * self::GIB, $usage->total_bytes);
        $this->assertSame(0, $usage->reported_bytes);
    }

    public function test_report_to_stripe_skips_when_storage_price_unconfigured(): void
    {
        config()->set('kinhold.billing_enabled', true);
        config()->set('kinhold.billing.storage.stripe_price_id', null);

        [$family, $owner, $category] = $this->familySetup();
        $entry = $this->vaultEntry($family, $category, $owner);
        $this->attachDocument($entry, $owner, 6 * self::GIB);

        $usage = app(StorageMeteringService::class)->reportToStripe($family);

        $this->assertSame(0, $usage->reported_bytes);
    }

    public function test_tally_all_recalculates_every_family_without_pushing_when_unsubscribed(): void
    {
        [$familyA, $ownerA, $categoryA] = $this->familySetup();
        [$familyB, $ownerB, $categoryB] = $this->familySetup();

        $entryA = $this->vaultEntry($familyA, $categoryA, $ownerA);
        $entryB = $this->vaultEntry($familyB, $categoryB, $ownerB);

        $this->attachDocument($entryA, $ownerA, 1 * self::GIB);
        $this->attachDocument($entryB, $ownerB, 2 * self::GIB);

        $stats = app(StorageMeteringService::class)->tallyAll();

        $this->assertSame(2, $stats['recalculated']);
        $this->assertSame(0, $stats['reported']);
        $this->assertSame(1 * self::GIB, FamilyStorageUsage::where('family_id', $familyA->id)->value('total_bytes'));
        $this->assertSame(2 * self::GIB, FamilyStorageUsage::where('family_id', $familyB->id)->value('total_bytes'));
    }

    public function test_artisan_command_runs_cleanly_on_empty_state(): void
    {
        $this->artisan('kinhold:tally-storage')
            ->expectsOutputToContain('Recalculated 0 families')
            ->assertSuccessful();
    }
}
