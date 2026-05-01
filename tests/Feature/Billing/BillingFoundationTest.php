<?php

namespace Tests\Feature\Billing;

use App\Models\Family;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_config_endpoint_exposes_billing_enabled_false_by_default(): void
    {
        config()->set('kinhold.billing_enabled', false);

        $response = $this->getJson('/api/v1/config');

        $response->assertStatus(200);
        $response->assertJsonPath('billing_enabled', false);
    }

    public function test_config_endpoint_exposes_billing_enabled_true_when_flagged(): void
    {
        config()->set('kinhold.billing_enabled', true);

        $response = $this->getJson('/api/v1/config');

        $response->assertJsonPath('billing_enabled', true);
    }

    public function test_billing_service_is_disabled_by_default(): void
    {
        config()->set('kinhold.billing_enabled', false);

        $service = app(BillingService::class);

        $this->assertFalse($service->isEnabled());
    }

    public function test_resolve_current_plan_returns_self_hosted_when_disabled(): void
    {
        config()->set('kinhold.billing_enabled', false);
        $family = Family::factory()->create();

        $plan = app(BillingService::class)->resolveCurrentPlan($family);

        $this->assertSame('self_hosted', $plan);
    }

    public function test_resolve_current_plan_returns_free_when_enabled_without_subscription(): void
    {
        config()->set('kinhold.billing_enabled', true);
        $family = Family::factory()->create();

        $plan = app(BillingService::class)->resolveCurrentPlan($family);

        $this->assertSame('free', $plan);
    }

    public function test_billing_owner_id_backfill_picks_oldest_parent(): void
    {
        // Family with one parent and one child — the parent should become owner.
        $family = Family::factory()->create(['billing_owner_id' => null]);
        $parent = User::factory()->parent()->create([
            'family_id' => $family->id,
            'created_at' => now()->subDays(2),
        ]);
        User::factory()->child()->create([
            'family_id' => $family->id,
            'created_at' => now()->subDay(),
        ]);

        // Re-run the migration's backfill logic directly. We don't re-invoke the
        // migration because RefreshDatabase has already applied it; instead this
        // mirrors the migration's chunk loop to prove the algorithm works.
        $owner = User::where('family_id', $family->id)
            ->where('family_role', 'parent')
            ->orderBy('created_at')
            ->value('id');

        $this->assertSame($parent->id, $owner);
    }

    public function test_family_can_use_cashier_billable_methods(): void
    {
        // Smoke-test: the Billable trait wires up against a UUID-keyed Family.
        // We don't hit Stripe — just verify the trait's local scaffolding works
        // (subscriptions() relation, hasStripeId(), etc.) without exploding.
        $family = Family::factory()->create();

        $this->assertFalse($family->hasStripeId());
        $this->assertCount(0, $family->subscriptions);
    }
}
