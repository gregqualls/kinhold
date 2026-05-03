<?php

namespace Tests\Feature\Billing;

use App\Models\Family;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DemoFamilyBillingLockdownTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('kinhold.billing_enabled', true);
        config()->set('kinhold.billing.base_plan.stripe_price_id', 'price_test_base');
    }

    private function demoFamilyOwner(): array
    {
        $family = Family::factory()->create(['slug' => 'q32-demo-family']);
        $owner = User::factory()->parent()->create(['family_id' => $family->id]);
        $family->update(['billing_owner_id' => $owner->id]);

        return [$family, $owner];
    }

    public function test_billing_service_identifies_demo_family_by_slug(): void
    {
        $service = app(BillingService::class);

        $demo = Family::factory()->create(['slug' => 'q32-demo-family']);
        $real = Family::factory()->create(['slug' => 'real-family']);

        $this->assertTrue($service->isDemoFamily($demo));
        $this->assertFalse($service->isDemoFamily($real));
    }

    public function test_billing_endpoints_return_403_for_demo_family(): void
    {
        [, $owner] = $this->demoFamilyOwner();
        Sanctum::actingAs($owner);

        $endpoints = [
            ['get', '/api/v1/billing/current', []],
            ['post', '/api/v1/billing/checkout-session', [
                'success_url' => 'https://example.com/ok',
                'cancel_url' => 'https://example.com/cancel',
            ]],
            ['post', '/api/v1/billing/portal-session', [
                'return_url' => 'https://example.com/back',
            ]],
            ['post', '/api/v1/billing/cancel', []],
            ['post', '/api/v1/billing/resume', []],
            ['post', '/api/v1/billing/ai-tier', ['tier' => 'lite']],
        ];

        foreach ($endpoints as [$method, $url, $payload]) {
            $response = $this->json(strtoupper($method), $url, $payload);
            $response->assertStatus(403);
            $response->assertJsonPath('message', 'Billing is disabled for the demo. Sign up at kinhold.app to manage a real subscription.');
        }
    }

    public function test_real_family_billing_endpoints_still_work(): void
    {
        $family = Family::factory()->create(['slug' => 'real-family']);
        $owner = User::factory()->parent()->create(['family_id' => $family->id]);
        $family->update(['billing_owner_id' => $owner->id]);

        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/billing/current')->assertStatus(200);
    }

    public function test_auth_user_endpoint_exposes_is_demo_flag(): void
    {
        [, $owner] = $this->demoFamilyOwner();
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/user')
            ->assertStatus(200)
            ->assertJsonPath('family.is_demo', true);

        $realFamily = Family::factory()->create(['slug' => 'real-family']);
        $realOwner = User::factory()->parent()->create(['family_id' => $realFamily->id]);
        Sanctum::actingAs($realOwner);

        $this->getJson('/api/v1/user')
            ->assertStatus(200)
            ->assertJsonPath('family.is_demo', false);
    }

    public function test_onboarding_status_marks_billing_step_complete_for_demo(): void
    {
        [, $owner] = $this->demoFamilyOwner();
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/onboarding/status')
            ->assertStatus(200)
            ->assertJsonPath('billing_step_complete', true);
    }
}
