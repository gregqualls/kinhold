<?php

namespace Tests\Feature\Billing;

use App\Models\Family;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Asserts the `family.billing` block surfaced on `/api/v1/user` is the shape
 * the SPA paywall composable expects (#223 / 70-I). The composable consumes
 * `requires_payment`, `paywall_reason`, `is_billing_owner`, and
 * `billing_owner_name` — drift here breaks the gate without any backend test
 * failing, so the contract is locked in here.
 */
class SubscriptionPaywallGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('kinhold.billing_enabled', true);
    }

    private function familyWithOwner(array $familyAttrs = []): array
    {
        $family = Family::factory()->create(array_merge([
            'stripe_id' => 'cus_'.bin2hex(random_bytes(6)),
        ], $familyAttrs));
        $owner = User::factory()->parent()->create([
            'family_id' => $family->id,
            'name' => 'Greg',
        ]);
        $family->update(['billing_owner_id' => $owner->id]);

        return [$family->fresh(), $owner];
    }

    private function attachSubscription(Family $family, array $overrides = []): void
    {
        $family->subscriptions()->create(array_merge([
            'type' => 'default',
            'stripe_id' => 'sub_'.bin2hex(random_bytes(6)),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_base',
            'quantity' => 1,
        ], $overrides));
    }

    public function test_billing_owner_with_expired_trial_sees_actionable_paywall(): void
    {
        [$family, $owner] = $this->familyWithOwner();
        $this->attachSubscription($family, [
            'stripe_status' => 'incomplete',
            'trial_ends_at' => Carbon::now()->subDay(),
        ]);
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonPath('family.billing.requires_payment', true)
            ->assertJsonPath('family.billing.paywall_reason', 'trial_expired')
            ->assertJsonPath('family.billing.is_billing_owner', true)
            ->assertJsonPath('family.billing.billing_owner_name', 'Greg');
    }

    public function test_non_owner_parent_sees_paywall_without_action(): void
    {
        [$family] = $this->familyWithOwner();
        $this->attachSubscription($family, [
            'stripe_status' => 'incomplete',
            'trial_ends_at' => Carbon::now()->subDay(),
        ]);
        $other = User::factory()->parent()->create(['family_id' => $family->id]);
        Sanctum::actingAs($other);

        $this->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonPath('family.billing.requires_payment', true)
            ->assertJsonPath('family.billing.is_billing_owner', false)
            ->assertJsonPath('family.billing.billing_owner_name', 'Greg');
    }

    public function test_active_subscription_does_not_paywall(): void
    {
        [$family, $owner] = $this->familyWithOwner();
        $this->attachSubscription($family, ['stripe_status' => 'active']);
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonPath('family.billing.requires_payment', false)
            ->assertJsonPath('family.billing.paywall_reason', null);
    }

    public function test_trialing_does_not_paywall(): void
    {
        [$family, $owner] = $this->familyWithOwner();
        $this->attachSubscription($family, [
            'stripe_status' => 'trialing',
            'trial_ends_at' => Carbon::now()->addDays(7),
        ]);
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonPath('family.billing.requires_payment', false);
    }

    public function test_grace_period_does_not_paywall(): void
    {
        [$family, $owner] = $this->familyWithOwner();
        $family->forceFill(['payment_failed_at' => Carbon::now()->subDays(2)])->save();
        $this->attachSubscription($family, ['stripe_status' => 'past_due']);
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonPath('family.billing.requires_payment', false);
    }

    public function test_self_hosted_returns_null_billing_block(): void
    {
        config()->set('kinhold.billing_enabled', false);
        [, $owner] = $this->familyWithOwner();
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonPath('family.billing', null);
    }

    public function test_past_due_paywalls_with_reason(): void
    {
        [$family, $owner] = $this->familyWithOwner();
        $this->attachSubscription($family, ['stripe_status' => 'past_due']);
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonPath('family.billing.requires_payment', true)
            ->assertJsonPath('family.billing.paywall_reason', 'past_due');
    }
}
