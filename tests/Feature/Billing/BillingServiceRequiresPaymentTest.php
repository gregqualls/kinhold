<?php

namespace Tests\Feature\Billing;

use App\Models\Family;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Truth table for `BillingService::paywallReason()` / `requiresPayment()` —
 * the SPA-side paywall splash gate (#223 / 70-I). Each branch in the state
 * machine gets its own assertion so the v1.9.0 BILLING_ENABLED flip can't
 * silently lock out a legitimate state (or worse, fail to lock out an
 * illegitimate one).
 */
class BillingServiceRequiresPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('kinhold.billing_enabled', true);
    }

    private function service(): BillingService
    {
        return $this->app->make(BillingService::class);
    }

    private function familyWithStripe(): Family
    {
        $family = Family::factory()->create([
            'stripe_id' => 'cus_'.bin2hex(random_bytes(6)),
        ]);
        User::factory()->parent()->create(['family_id' => $family->id])
            ->each(fn ($u) => $family->update(['billing_owner_id' => $u->id]));

        return $family->fresh();
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

    public function test_returns_false_when_billing_disabled(): void
    {
        config()->set('kinhold.billing_enabled', false);
        $family = $this->familyWithStripe();
        $this->attachSubscription($family, [
            'stripe_status' => 'past_due',
        ]);

        $this->assertFalse($this->service()->requiresPayment($family->fresh()));
        $this->assertNull($this->service()->paywallReason($family->fresh()));
    }

    public function test_returns_false_when_no_stripe_customer(): void
    {
        $family = Family::factory()->create();
        // No subscription, no stripe_id — onboarding territory.

        $this->assertFalse($this->service()->requiresPayment($family));
    }

    public function test_returns_false_for_active_subscription(): void
    {
        $family = $this->familyWithStripe();
        $this->attachSubscription($family, ['stripe_status' => 'active']);

        $this->assertFalse($this->service()->requiresPayment($family->fresh()));
    }

    public function test_returns_false_for_trialing_subscription(): void
    {
        $family = $this->familyWithStripe();
        $this->attachSubscription($family, [
            'stripe_status' => 'trialing',
            'trial_ends_at' => Carbon::now()->addDays(7),
        ]);

        $this->assertFalse($this->service()->requiresPayment($family->fresh()));
    }

    public function test_returns_false_during_grace_period(): void
    {
        $family = $this->familyWithStripe();
        $family->forceFill(['payment_failed_at' => Carbon::now()->subDays(3)])->save();
        $this->attachSubscription($family, ['stripe_status' => 'past_due']);

        $this->assertFalse($this->service()->requiresPayment($family->fresh()));
    }

    public function test_returns_trial_expired_when_trial_ended_without_paid_sub(): void
    {
        $family = $this->familyWithStripe();
        $this->attachSubscription($family, [
            'stripe_status' => 'incomplete',
            'trial_ends_at' => Carbon::now()->subDay(),
        ]);

        $this->assertSame('trial_expired', $this->service()->paywallReason($family->fresh()));
        $this->assertTrue($this->service()->requiresPayment($family->fresh()));
    }

    public function test_returns_past_due_for_past_due_status(): void
    {
        $family = $this->familyWithStripe();
        $this->attachSubscription($family, ['stripe_status' => 'past_due']);

        $this->assertSame('past_due', $this->service()->paywallReason($family->fresh()));
    }

    public function test_returns_past_due_for_unpaid_status(): void
    {
        $family = $this->familyWithStripe();
        $this->attachSubscription($family, ['stripe_status' => 'unpaid']);

        $this->assertSame('past_due', $this->service()->paywallReason($family->fresh()));
    }

    public function test_returns_past_due_for_incomplete_expired_status(): void
    {
        $family = $this->familyWithStripe();
        $this->attachSubscription($family, ['stripe_status' => 'incomplete_expired']);

        $this->assertSame('past_due', $this->service()->paywallReason($family->fresh()));
    }

    public function test_returns_cancelled_expired_when_ends_at_in_past(): void
    {
        $family = $this->familyWithStripe();
        $this->attachSubscription($family, [
            'stripe_status' => 'canceled',
            'ends_at' => Carbon::now()->subDay(),
        ]);

        $this->assertSame('cancelled_expired', $this->service()->paywallReason($family->fresh()));
    }

    public function test_returns_false_when_cancelled_but_still_in_grace_period(): void
    {
        $family = $this->familyWithStripe();
        // Cashier-style "cancelled with grace": stripe_status active, ends_at future.
        $this->attachSubscription($family, [
            'stripe_status' => 'active',
            'ends_at' => Carbon::now()->addDays(10),
        ]);

        $this->assertFalse($this->service()->requiresPayment($family->fresh()));
    }
}
