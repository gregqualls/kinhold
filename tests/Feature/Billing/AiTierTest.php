<?php

namespace Tests\Feature\Billing;

use App\Models\Family;
use App\Models\User;
use App\Services\AiUsageService;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class AiTierTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('kinhold.billing_enabled', true);
        config()->set('kinhold.billing.base_plan.stripe_price_id', 'price_test_base');
        config()->set('kinhold.chatbot.plans.lite.stripe_price_id', 'price_test_ai_lite');
        config()->set('kinhold.chatbot.plans.standard.stripe_price_id', 'price_test_ai_standard');
        config()->set('kinhold.chatbot.plans.pro.stripe_price_id', 'price_test_ai_pro');
    }

    /** Family with a parent who is also the billing owner. */
    private function ownerSetup(): array
    {
        $family = Family::factory()->create();
        $owner = User::factory()->parent()->create(['family_id' => $family->id]);
        $family->update(['billing_owner_id' => $owner->id]);

        return [$family, $owner];
    }

    public function test_returns_403_when_billing_disabled(): void
    {
        config()->set('kinhold.billing_enabled', false);

        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/ai-tier', ['tier' => 'lite'])->assertStatus(403);
    }

    public function test_returns_403_for_non_billing_owner(): void
    {
        [$family] = $this->ownerSetup();
        $otherParent = User::factory()->parent()->create(['family_id' => $family->id]);
        Sanctum::actingAs($otherParent);

        $this->postJson('/api/v1/billing/ai-tier', ['tier' => 'lite'])->assertStatus(403);
    }

    public function test_returns_422_for_invalid_tier_slug(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/ai-tier', ['tier' => 'enterprise'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['tier']);
    }

    public function test_returns_422_when_no_base_subscription(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/ai-tier', ['tier' => 'lite'])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Subscribe to the base plan before adding an AI tier.');
    }

    public function test_success_delegates_to_service_and_returns_summary(): void
    {
        [$family, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $mock = Mockery::mock(BillingService::class)->makePartial();
        $mock->shouldReceive('isEnabled')->andReturn(true);
        $mock->shouldReceive('selectAiTier')->once()->with(
            Mockery::on(fn ($f) => $f->id === $family->id),
            'lite',
        );
        $mock->shouldReceive('summary')->andReturn(['plan' => 'base', 'ai_tier' => ['mode' => 'kinhold', 'plan' => 'lite']]);
        $this->app->instance(BillingService::class, $mock);

        $this->postJson('/api/v1/billing/ai-tier', ['tier' => 'lite'])
            ->assertStatus(200)
            ->assertJsonPath('ai_tier.plan', 'lite');
    }

    public function test_summary_includes_ai_tier_block(): void
    {
        [, $owner] = $this->ownerSetup();
        Sanctum::actingAs($owner);

        $response = $this->getJson('/api/v1/billing/current');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'ai_tier' => ['mode', 'plan', 'usage', 'tiers'],
        ]);
        $response->assertJsonPath('ai_tier.mode', 'kinhold');

        // Tiers list reflects only public + (we don't gate by `configured`
        // here — the SPA disables based on it). Three configured tiers.
        $response->assertJsonCount(3, 'ai_tier.tiers');
    }

    public function test_daily_cap_resolves_from_chatbot_plan_slug(): void
    {
        // Regression: setting families.settings.chatbot.plan must flow through
        // AiUsageService::limitFor() so the existing enforcement code applies
        // the right cap after a tier purchase.
        $family = Family::factory()->create([
            'settings' => ['chatbot' => ['plan' => 'lite']],
        ]);

        $this->assertSame(50, app(AiUsageService::class)->limitFor($family));

        $family->update(['settings' => ['chatbot' => ['plan' => 'pro']]]);
        $this->assertSame(300, app(AiUsageService::class)->limitFor($family->fresh()));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
