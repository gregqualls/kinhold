<?php

namespace Tests\Feature\Billing;

use App\Models\Family;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Checkout;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class BillingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('kinhold.billing_enabled', true);
        config()->set('kinhold.billing.base_plan.stripe_price_id', 'price_test_base');
    }

    /** Helper: family with a parent who is also the billing owner. */
    private function ownerSetup(): array
    {
        $family = Family::factory()->create();
        $owner = User::factory()->parent()->create(['family_id' => $family->id]);
        $family->update(['billing_owner_id' => $owner->id]);

        return [$family, $owner];
    }

    public function test_current_returns_summary_for_billing_owner(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $response = $this->getJson('/api/v1/billing/current');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'plan',
            'status',
            'on_trial',
            'on_grace_period',
            'cancelled',
            'trial_ends_at',
            'ends_at',
            'payment_method',
        ]);
        $response->assertJsonPath('plan', 'none');
    }

    public function test_current_returns_403_when_billing_disabled(): void
    {
        config()->set('kinhold.billing_enabled', false);

        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/billing/current')->assertStatus(403);
    }

    public function test_current_returns_403_for_parent_who_is_not_billing_owner(): void
    {
        [$family] = $this->ownerSetup();
        $otherParent = User::factory()->parent()->create(['family_id' => $family->id]);
        Sanctum::actingAs($otherParent);

        $this->getJson('/api/v1/billing/current')->assertStatus(403);
    }

    public function test_current_returns_403_for_child(): void
    {
        [$family] = $this->ownerSetup();
        $child = User::factory()->child()->create(['family_id' => $family->id]);
        Sanctum::actingAs($child);

        $this->getJson('/api/v1/billing/current')->assertStatus(403);
    }

    public function test_current_returns_401_when_unauthenticated(): void
    {
        $this->getJson('/api/v1/billing/current')->assertStatus(401);
    }

    public function test_checkout_session_validates_required_urls(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/checkout-session', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['success_url', 'cancel_url']);
    }

    public function test_checkout_session_returns_url_from_billing_service(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $stripeSession = (object) ['url' => 'https://checkout.stripe.com/test-session'];
        $checkout = Mockery::mock(Checkout::class);
        $checkout->shouldReceive('asStripeCheckoutSession')->andReturn($stripeSession);

        $mock = Mockery::mock(BillingService::class)->makePartial();
        $mock->shouldReceive('isEnabled')->andReturn(true);
        $mock->shouldReceive('createBaseCheckout')->once()->andReturn($checkout);
        $this->app->instance(BillingService::class, $mock);

        $this->postJson('/api/v1/billing/checkout-session', [
            'success_url' => 'https://example.test/settings#billing',
            'cancel_url' => 'https://example.test/settings#billing',
        ])
            ->assertStatus(200)
            ->assertJson(['url' => 'https://checkout.stripe.com/test-session']);
    }

    public function test_checkout_session_forwards_ai_tier_to_billing_service(): void
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
            ->withArgs(fn ($f, $success, $cancel, $tier) => $f->id === $family->id && $tier === 'standard')
            ->andReturn($checkout);
        $this->app->instance(BillingService::class, $mock);

        $this->postJson('/api/v1/billing/checkout-session', [
            'success_url' => 'https://example.test/settings#billing',
            'cancel_url' => 'https://example.test/settings#billing',
            'ai_tier' => 'standard',
        ])->assertStatus(200);
    }

    public function test_checkout_session_returns_403_when_billing_disabled(): void
    {
        config()->set('kinhold.billing_enabled', false);

        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/checkout-session', [
            'success_url' => 'https://example.test/settings',
            'cancel_url' => 'https://example.test/settings',
        ])->assertStatus(403);
    }

    public function test_portal_session_returns_422_without_stripe_id(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/portal-session', [
            'return_url' => 'https://example.test/settings',
        ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'No active subscription. Start a checkout first.');
    }

    public function test_portal_session_validates_return_url(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/portal-session', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['return_url']);
    }

    public function test_cancel_returns_422_without_active_subscription(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/cancel')
            ->assertStatus(422)
            ->assertJsonPath('message', 'No active subscription to cancel.');
    }

    public function test_resume_returns_422_without_grace_period(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/resume')
            ->assertStatus(422)
            ->assertJsonPath('message', 'No cancelled subscription to resume.');
    }

    public function test_cancel_returns_403_for_non_billing_owner(): void
    {
        [$family] = $this->ownerSetup();
        $otherParent = User::factory()->parent()->create(['family_id' => $family->id]);
        Sanctum::actingAs($otherParent);

        $this->postJson('/api/v1/billing/cancel')->assertStatus(403);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
