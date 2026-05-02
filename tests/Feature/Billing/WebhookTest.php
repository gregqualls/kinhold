<?php

namespace Tests\Feature\Billing;

use App\Models\Family;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Notifications\Billing\PaymentFailedNotification;
use App\Notifications\Billing\SubscriptionCancelledNotification;
use App\Notifications\Billing\SubscriptionResumedNotification;
use App\Notifications\Billing\TrialEndingNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_test_secret_for_unit_tests';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cashier.webhook.secret', self::SECRET);
        config()->set('cashier.webhook.tolerance', 300);
        config()->set('kinhold.billing_enabled', true);
    }

    /** Build a Stripe-style signed request hitting our webhook endpoint. */
    private function postSignedWebhook(array $payload): TestResponse
    {
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $timestamp = (string) time();
        $signedPayload = $timestamp.'.'.$body;
        $signature = hash_hmac('sha256', $signedPayload, self::SECRET);

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

    private function familyWithOwnerAndStripeId(?string $stripeId = null): array
    {
        $stripeId ??= 'cus_test_'.bin2hex(random_bytes(8));
        $family = Family::factory()->create([
            'stripe_id' => $stripeId,
        ]);
        $owner = User::factory()->parent()->create(['family_id' => $family->id]);
        $family->update(['billing_owner_id' => $owner->id]);

        return [$family->fresh(), $owner];
    }

    public function test_invoice_payment_failed_sets_grace_marker_and_notifies_owner(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwnerAndStripeId();

        $response = $this->postSignedWebhook([
            'id' => 'evt_test_failed_'.uniqid(),
            'type' => 'invoice.payment_failed',
            'data' => ['object' => ['customer' => $family->stripe_id]],
        ]);

        $response->assertStatus(200);
        $this->assertNotNull($family->fresh()->payment_failed_at);
        Notification::assertSentTo($owner, PaymentFailedNotification::class);
    }

    public function test_invoice_paid_clears_grace_marker_and_sends_resumed_email_when_was_failing(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwnerAndStripeId();
        $family->forceFill(['payment_failed_at' => Carbon::now()->subDays(2)])->save();

        $response = $this->postSignedWebhook([
            'id' => 'evt_test_paid_'.uniqid(),
            'type' => 'invoice.paid',
            'data' => ['object' => ['customer' => $family->stripe_id]],
        ]);

        $response->assertStatus(200);
        $this->assertNull($family->fresh()->payment_failed_at);
        Notification::assertSentTo($owner, SubscriptionResumedNotification::class);
    }

    public function test_invoice_paid_no_op_when_no_grace_period(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwnerAndStripeId();

        $this->postSignedWebhook([
            'id' => 'evt_test_paid_noop_'.uniqid(),
            'type' => 'invoice.paid',
            'data' => ['object' => ['customer' => $family->stripe_id]],
        ])->assertStatus(200);

        Notification::assertNothingSentTo($owner);
    }

    public function test_subscription_trial_will_end_sends_trial_ending_email(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwnerAndStripeId();

        $this->postSignedWebhook([
            'id' => 'evt_test_trial_'.uniqid(),
            'type' => 'customer.subscription.trial_will_end',
            'data' => ['object' => [
                'customer' => $family->stripe_id,
                'trial_end' => Carbon::now()->addDays(3)->timestamp,
            ]],
        ])->assertStatus(200);

        Notification::assertSentTo($owner, TrialEndingNotification::class);
    }

    public function test_subscription_deleted_sends_cancellation_email_for_default_sub(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwnerAndStripeId();

        $stripeSubId = 'sub_test_default_'.uniqid();
        $family->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => $stripeSubId,
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_base',
            'quantity' => 1,
        ]);

        $this->postSignedWebhook([
            'id' => 'evt_test_cancel_'.uniqid(),
            'type' => 'customer.subscription.deleted',
            'data' => ['object' => [
                'id' => $stripeSubId,
                'customer' => $family->stripe_id,
            ]],
        ])->assertStatus(200);

        Notification::assertSentTo($owner, SubscriptionCancelledNotification::class);
    }

    public function test_subscription_deleted_skips_email_for_non_default_sub(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwnerAndStripeId();

        // Family has a 'default' subscription, but Stripe is telling us a
        // different subscription was deleted (e.g., a hypothetical add-on).
        $family->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_default_kept',
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_base',
            'quantity' => 1,
        ]);

        $this->postSignedWebhook([
            'id' => 'evt_test_cancel_other_'.uniqid(),
            'type' => 'customer.subscription.deleted',
            'data' => ['object' => [
                'id' => 'sub_some_other_addon',
                'customer' => $family->stripe_id,
            ]],
        ])->assertStatus(200);

        Notification::assertNotSentTo($owner, SubscriptionCancelledNotification::class);
    }

    public function test_trial_will_end_handles_missing_trial_end_field(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwnerAndStripeId();

        // Defensive: Stripe always sends trial_end on this event, but we
        // shouldn't crash if a malformed payload arrives.
        $this->postSignedWebhook([
            'id' => 'evt_test_trial_no_end_'.uniqid(),
            'type' => 'customer.subscription.trial_will_end',
            'data' => ['object' => ['customer' => $family->stripe_id]],
        ])->assertStatus(200);

        Notification::assertSentTo($owner, TrialEndingNotification::class);
    }

    public function test_replaying_same_event_id_is_idempotent(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwnerAndStripeId();

        $eventId = 'evt_test_dup_'.uniqid();
        $payload = [
            'id' => $eventId,
            'type' => 'invoice.payment_failed',
            'data' => ['object' => ['customer' => $family->stripe_id]],
        ];

        $this->postSignedWebhook($payload)->assertStatus(200);
        $this->postSignedWebhook($payload)->assertStatus(200);
        $this->postSignedWebhook($payload)->assertStatus(200);

        // Webhook event recorded exactly once.
        $this->assertSame(1, WebhookEvent::query()
            ->where('provider', 'stripe')
            ->where('event_id', $eventId)
            ->count());

        // Notification fires exactly once despite three deliveries.
        Notification::assertSentToTimes($owner, PaymentFailedNotification::class, 1);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $body = json_encode(['id' => 'evt_x', 'type' => 'invoice.paid', 'data' => ['object' => ['customer' => 'cus_x']]]);

        $response = $this->call(
            'POST',
            '/api/v1/stripe/webhook',
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => 't='.time().',v1=deadbeef',
                'CONTENT_TYPE' => 'application/json',
            ],
            $body,
        );

        $this->assertSame(403, $response->getStatusCode());
    }

    public function test_invoice_payment_failed_does_not_overwrite_existing_grace_timestamp(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwnerAndStripeId();

        $original = Carbon::now()->subDays(4);
        $family->forceFill(['payment_failed_at' => $original])->save();

        $this->postSignedWebhook([
            'id' => 'evt_test_dupfail_'.uniqid(),
            'type' => 'invoice.payment_failed',
            'data' => ['object' => ['customer' => $family->stripe_id]],
        ])->assertStatus(200);

        $this->assertSame(
            $original->startOfSecond()->timestamp,
            $family->fresh()->payment_failed_at->startOfSecond()->timestamp,
        );
        Notification::assertNotSentTo($owner, PaymentFailedNotification::class);
    }
}
