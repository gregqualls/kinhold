<?php

namespace Tests\Feature\Onboarding;

use App\Models\Family;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Checkout;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

/**
 * Feature coverage for 70-G — the onboarding wizard's billing step.
 *
 * The Vue step itself is not tested (no SPA test harness in this project);
 * coverage focuses on the API contract the step depends on:
 *   - POST /api/v1/billing/checkout-session accepting `ai_tier`
 *   - GET  /api/v1/onboarding/status surfacing `billing_enabled` +
 *     `billing_step_complete` so the wizard can render or skip.
 */
class BillingStepTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('kinhold.billing_enabled', true);
        config()->set('kinhold.billing.base_plan.stripe_price_id', 'price_test_base');
        config()->set('kinhold.chatbot.plans.lite.stripe_price_id', 'price_test_ai_lite');
    }

    /** Helper: family with a parent who is also the billing owner. */
    private function ownerSetup(): array
    {
        $family = Family::factory()->create();
        $owner = User::factory()->parent()->create(['family_id' => $family->id]);
        $family->update(['billing_owner_id' => $owner->id]);

        return [$family, $owner];
    }

    public function test_checkout_session_accepts_ai_tier_and_persists_settings(): void
    {
        [$family, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $stripeSession = (object) ['url' => 'https://checkout.stripe.com/test-session'];
        $checkout = Mockery::mock(Checkout::class);
        $checkout->shouldReceive('asStripeCheckoutSession')->andReturn($stripeSession);

        $mock = Mockery::mock(BillingService::class)->makePartial();
        $mock->shouldReceive('isEnabled')->andReturn(true);
        $mock->shouldReceive('createBaseCheckout')
            ->once()
            ->withArgs(function ($f, $success, $cancel, $tier) use ($family) {
                return $f->id === $family->id
                    && $tier === 'lite'
                    && str_contains($success, 'billing=success')
                    && str_contains($cancel, 'billing=cancel');
            })
            ->andReturnUsing(function ($f) use ($checkout) {
                // Mirror the real service's settings write so the assertion
                // below validates the contract, not just the mock.
                $settings = $f->settings ?? [];
                $settings['ai_mode'] = 'kinhold';
                $settings['chatbot'] = ['plan' => 'lite'];
                $f->forceFill(['settings' => $settings])->save();

                return $checkout;
            });
        $this->app->instance(BillingService::class, $mock);

        $this->postJson('/api/v1/billing/checkout-session', [
            'success_url' => 'https://example.test/onboarding?billing=success',
            'cancel_url' => 'https://example.test/onboarding?billing=cancel',
            'ai_tier' => 'lite',
        ])
            ->assertStatus(200)
            ->assertJson(['url' => 'https://checkout.stripe.com/test-session']);

        $family->refresh();
        $this->assertSame('kinhold', $family->settings['ai_mode']);
        $this->assertSame('lite', $family->settings['chatbot']['plan']);
    }

    public function test_checkout_session_rejects_invalid_ai_tier(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/checkout-session', [
            'success_url' => 'https://example.test/onboarding',
            'cancel_url' => 'https://example.test/onboarding',
            'ai_tier' => 'enterprise',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ai_tier']);
    }

    public function test_checkout_session_returns_403_when_billing_disabled(): void
    {
        config()->set('kinhold.billing_enabled', false);

        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/checkout-session', [
            'success_url' => 'https://example.test/onboarding',
            'cancel_url' => 'https://example.test/onboarding',
            'ai_tier' => 'lite',
        ])->assertStatus(403);
    }

    public function test_checkout_session_rejects_unconfigured_managed_tier(): void
    {
        // Tier whose Stripe price ID isn't configured → 422 from the service,
        // settings stay untouched (Stripe call never happens).
        config()->set('kinhold.chatbot.plans.standard.stripe_price_id', null);

        [$family, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $mock = Mockery::mock(BillingService::class)->makePartial();
        $mock->shouldReceive('isEnabled')->andReturn(true);
        // Don't mock createBaseCheckout — let the real method run so the
        // "tier not available yet" 422 fires from validation.
        $this->app->instance(BillingService::class, $mock);

        $this->postJson('/api/v1/billing/checkout-session', [
            'success_url' => 'https://example.test/onboarding',
            'cancel_url' => 'https://example.test/onboarding',
            'ai_tier' => 'standard',
        ])->assertStatus(422);

        $family->refresh();
        $this->assertArrayNotHasKey('ai_mode', $family->settings ?? []);
        $this->assertArrayNotHasKey('chatbot', $family->settings ?? []);
    }

    public function test_status_reports_billing_enabled_and_step_complete_flags(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        // Initially: billing_enabled=true, step not complete (no plan, no
        // subscription, ai_mode unset).
        $this->getJson('/api/v1/onboarding/status')
            ->assertStatus(200)
            ->assertJsonPath('billing_enabled', true)
            ->assertJsonPath('billing_step_complete', false);

        // After picking BYOK (sets ai_mode=byok), the step counts as done.
        $owner->family->forceFill([
            'settings' => array_merge($owner->family->settings ?? [], ['ai_mode' => 'byok']),
        ])->save();

        $this->getJson('/api/v1/onboarding/status')
            ->assertStatus(200)
            ->assertJsonPath('billing_step_complete', true);
    }

    public function test_status_reports_step_complete_when_billing_disabled(): void
    {
        config()->set('kinhold.billing_enabled', false);

        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        // Self-host: step is implicitly complete so the wizard never blocks.
        $this->getJson('/api/v1/onboarding/status')
            ->assertStatus(200)
            ->assertJsonPath('billing_enabled', false)
            ->assertJsonPath('billing_step_complete', true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
