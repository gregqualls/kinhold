<?php

namespace Tests\Feature\Billing;

use App\Models\Family;
use App\Models\User;
use App\Notifications\Billing\LiteTrialEndedNotification;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Issue #230 — trial includes Lite free; upgrade to Standard/Pro ends trial early.
 * Stripe-network paths (endTrial / addPriceAndInvoice) are exercised at the
 * Cashier integration level — these tests cover the trial-aware branch logic
 * and side effects that don't require an outbound Stripe call.
 */
class TrialAiTierTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_test_secret_for_unit_tests';

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('kinhold.billing_enabled', true);
        config()->set('kinhold.billing.base_plan.stripe_price_id', 'price_test_base');
        config()->set('kinhold.chatbot.plans.lite.stripe_price_id', 'price_test_ai_lite');
        config()->set('kinhold.chatbot.plans.standard.stripe_price_id', 'price_test_ai_standard');
        config()->set('kinhold.chatbot.plans.pro.stripe_price_id', 'price_test_ai_pro');
        config()->set('cashier.webhook.secret', self::SECRET);
        config()->set('cashier.webhook.tolerance', 300);
    }

    /**
     * @param  bool  $withStripeCustomer  set true only for webhook tests that need
     *                                    Cashier::findBillable() to resolve the family
     *                                    by customer ID. Summary tests omit it so the
     *                                    summary() endpoint doesn't trigger a real
     *                                    Stripe defaultPaymentMethod() call.
     * @return array{0: Family, 1: User, 2: string} subscription stripe_id
     */
    private function trialingFamily(?array $settings = null, bool $withStripeCustomer = false): array
    {
        $stripeSubId = 'sub_trial_'.bin2hex(random_bytes(6));
        $attrs = ['settings' => $settings ?? []];
        if ($withStripeCustomer) {
            $attrs['stripe_id'] = 'cus_trial_'.bin2hex(random_bytes(6));
        }
        $family = Family::factory()->create($attrs);
        $owner = User::factory()->parent()->create(['family_id' => $family->id]);
        $family->update(['billing_owner_id' => $owner->id]);
        $family->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => $stripeSubId,
            'stripe_status' => 'trialing',
            'stripe_price' => 'price_test_base',
            'quantity' => 1,
            'trial_ends_at' => Carbon::now()->addDays(10),
        ]);

        return [$family->fresh(), $owner, $stripeSubId];
    }

    /**
     * Build a `customer.subscription.updated` payload Cashier's parent handler
     * accepts (it reads `items.data[0].price.{id,product}` to update the
     * subscriptions row, so a bare object would 500 in the parent call).
     */
    private function subscriptionUpdatedPayload(string $customerId, string $subStripeId, string $status, ?string $previousStatus): array
    {
        return [
            'id' => 'evt_sub_updated_'.uniqid(),
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => $subStripeId,
                    'customer' => $customerId,
                    'status' => $status,
                    'items' => [
                        'data' => [[
                            'id' => 'si_'.uniqid(),
                            'price' => ['id' => 'price_test_base', 'product' => 'prod_test_base'],
                            'quantity' => 1,
                        ]],
                    ],
                ],
                'previous_attributes' => $previousStatus !== null ? ['status' => $previousStatus] : [],
            ],
        ];
    }

    private function postSignedWebhook(array $payload): TestResponse
    {
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $timestamp = (string) time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$body, self::SECRET);

        return $this->call(
            'POST',
            '/api/v1/stripe/webhook',
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
                'CONTENT_TYPE' => 'application/json',
            ],
            $body,
        );
    }

    public function test_summary_surfaces_included_in_trial_lite_during_trial(): void
    {
        [, $owner] = $this->trialingFamily();
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/billing/current')
            ->assertStatus(200)
            ->assertJsonPath('ai_tier.on_trial', true)
            ->assertJsonPath('ai_tier.included_in_trial', 'lite');
    }

    public function test_summary_omits_included_in_trial_when_not_trialing(): void
    {
        $family = Family::factory()->create();
        $owner = User::factory()->parent()->create(['family_id' => $family->id]);
        $family->update(['billing_owner_id' => $owner->id]);
        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/billing/current')
            ->assertStatus(200)
            ->assertJsonPath('ai_tier.on_trial', false)
            ->assertJsonPath('ai_tier.included_in_trial', null);
    }

    public function test_select_lite_during_trial_writes_settings_without_stripe_call(): void
    {
        [$family] = $this->trialingFamily();

        // No Stripe price reconciliation should happen — no items existed and
        // none are added. The Stripe-touching methods on Subscription would
        // throw with our fake stripe_id, so a successful return proves we
        // short-circuited correctly.
        app(BillingService::class)->selectAiTier($family, 'lite');

        $fresh = $family->fresh();
        $this->assertSame('lite', $fresh->settings['chatbot']['plan']);
        $this->assertSame('kinhold', $fresh->settings['ai_mode']);
    }

    public function test_select_standard_during_trial_without_payment_method_returns_422(): void
    {
        [, $owner] = $this->trialingFamily();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/billing/ai-tier', ['tier' => 'standard'])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Add a payment method before upgrading mid-trial.');
    }

    public function test_subscription_updated_webhook_clears_implicit_lite_on_trial_to_active(): void
    {
        Notification::fake();
        [$family, $owner, $subId] = $this->trialingFamily(null, true);
        // Family had picked Lite explicitly during trial — same free path.
        $family->forceFill(['settings' => ['chatbot' => ['plan' => 'lite'], 'ai_mode' => 'kinhold']])->save();

        $this->postSignedWebhook(
            $this->subscriptionUpdatedPayload($family->stripe_id, $subId, 'active', 'trialing')
        )->assertStatus(200);

        $this->assertNull($family->fresh()->settings['chatbot']['plan'] ?? null);
        Notification::assertSentTo($owner, LiteTrialEndedNotification::class);
    }

    public function test_subscription_updated_webhook_clears_null_plan_on_trial_to_active(): void
    {
        // Implicit grant case: family never picked. Settings stay null after
        // the transition (no-op write but the handler shouldn't crash).
        // No email — null-plan families never saw a "Lite" UI label.
        Notification::fake();
        [$family, $owner, $subId] = $this->trialingFamily(null, true);

        $this->postSignedWebhook(
            $this->subscriptionUpdatedPayload($family->stripe_id, $subId, 'active', 'trialing')
        )->assertStatus(200);

        $this->assertNull($family->fresh()->settings['chatbot']['plan'] ?? null);
        Notification::assertNotSentTo($owner, LiteTrialEndedNotification::class);
    }

    public function test_subscription_updated_webhook_preserves_explicit_pro_pick(): void
    {
        Notification::fake();
        [$family, $owner, $subId] = $this->trialingFamily(['chatbot' => ['plan' => 'pro'], 'ai_mode' => 'kinhold'], true);

        $this->postSignedWebhook(
            $this->subscriptionUpdatedPayload($family->stripe_id, $subId, 'active', 'trialing')
        )->assertStatus(200);

        $this->assertSame('pro', $family->fresh()->settings['chatbot']['plan']);
        Notification::assertNotSentTo($owner, LiteTrialEndedNotification::class);
    }

    public function test_subscription_updated_webhook_ignores_non_trial_transitions(): void
    {
        Notification::fake();
        [$family, $owner, $subId] = $this->trialingFamily(['chatbot' => ['plan' => 'lite'], 'ai_mode' => 'kinhold'], true);

        // active → past_due is not a trial-end transition; plan must persist.
        $this->postSignedWebhook(
            $this->subscriptionUpdatedPayload($family->stripe_id, $subId, 'past_due', 'active')
        )->assertStatus(200);

        $this->assertSame('lite', $family->fresh()->settings['chatbot']['plan']);
        Notification::assertNotSentTo($owner, LiteTrialEndedNotification::class);
    }
}
