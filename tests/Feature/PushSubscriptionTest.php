<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\TestPushNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use NotificationChannels\WebPush\PushSubscription;
use Tests\TestCase;

class PushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_subscribe(): void
    {
        $response = $this->postJson('/api/v1/push/subscriptions', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
            'keys' => ['p256dh' => 'pk', 'auth' => 'auth'],
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_register_a_subscription(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/push/subscriptions', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/endpoint-1',
            'keys' => [
                'p256dh' => 'BPxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                'auth' => 'authsecret-abc',
            ],
            'content_encoding' => 'aesgcm',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $user->id,
            'subscribable_type' => $user->getMorphClass(),
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/endpoint-1',
        ]);
        $this->assertSame(1, $user->pushSubscriptions()->count());
    }

    public function test_re_registering_same_endpoint_updates_keys_in_place(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $endpoint = 'https://fcm.googleapis.com/fcm/send/refresh';
        $this->postJson('/api/v1/push/subscriptions', [
            'endpoint' => $endpoint,
            'keys' => ['p256dh' => 'old-pk', 'auth' => 'old-auth'],
        ])->assertStatus(201);

        $this->postJson('/api/v1/push/subscriptions', [
            'endpoint' => $endpoint,
            'keys' => ['p256dh' => 'new-pk', 'auth' => 'new-auth'],
        ])->assertStatus(201);

        $this->assertSame(1, PushSubscription::query()->where('endpoint', $endpoint)->count());
        $row = PushSubscription::query()->where('endpoint', $endpoint)->first();
        $this->assertSame('new-pk', $row->public_key);
        $this->assertSame('new-auth', $row->auth_token);
    }

    public function test_re_registering_endpoint_owned_by_another_user_reassigns_it(): void
    {
        $alice = User::factory()->create();
        $bob = User::factory()->create();
        $endpoint = 'https://fcm.googleapis.com/fcm/send/shared-device';

        Sanctum::actingAs($alice);
        $this->postJson('/api/v1/push/subscriptions', [
            'endpoint' => $endpoint,
            'keys' => ['p256dh' => 'pk', 'auth' => 'auth'],
        ])->assertStatus(201);

        Sanctum::actingAs($bob);
        $this->postJson('/api/v1/push/subscriptions', [
            'endpoint' => $endpoint,
            'keys' => ['p256dh' => 'pk', 'auth' => 'auth'],
        ])->assertStatus(201);

        $this->assertSame(0, $alice->pushSubscriptions()->count());
        $this->assertSame(1, $bob->pushSubscriptions()->count());
    }

    public function test_destroy_removes_only_callers_subscription(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $endpoint = 'https://fcm.googleapis.com/fcm/send/destroy-me';
        $this->postJson('/api/v1/push/subscriptions', [
            'endpoint' => $endpoint,
            'keys' => ['p256dh' => 'pk', 'auth' => 'auth'],
        ])->assertStatus(201);

        $this->deleteJson('/api/v1/push/subscriptions', [
            'endpoint' => $endpoint,
        ])->assertStatus(204);

        $this->assertSame(0, $user->pushSubscriptions()->count());
    }

    public function test_test_endpoint_requires_an_existing_subscription(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/push/subscriptions/test');

        $response->assertStatus(422);
    }

    public function test_typed_test_endpoint_requires_a_subscription(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/push/subscriptions/test/task_due_soon');

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'No push subscriptions registered for this user.']);
    }

    public function test_typed_test_endpoint_rejects_unknown_keys(): void
    {
        $user = User::factory()->create();
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/push/subscriptions/test/not_a_real_type');

        $response->assertStatus(422);
    }

    public function test_typed_test_endpoint_dispatches_each_supported_key(): void
    {
        $user = User::factory()->create();
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');
        Sanctum::actingAs($user);

        Notification::fake();

        // Each typed test endpoint runs the real notification's toWebPush() and
        // then wraps the resulting message in TestPushNotification so the dispatch
        // bypasses preference gating — clicking "Test" is explicit consent.
        $keys = ['task_due_soon', 'shopping_item_added', 'calendar_event_reminder', 'dinner_reminder'];
        foreach ($keys as $key) {
            $this->postJson("/api/v1/push/subscriptions/test/{$key}")->assertOk();
        }

        Notification::assertSentToTimes(
            $user,
            TestPushNotification::class,
            count($keys),
        );
    }
}
