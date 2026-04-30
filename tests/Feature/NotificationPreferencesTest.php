<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationPreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_defaults_for_a_fresh_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/settings/notification-preferences');

        $response->assertStatus(200);
        $response->assertJsonPath('preferences.muted', false);
        $response->assertJsonPath('preferences.quiet_hours.enabled', false);
        $response->assertJsonPath('preferences.dinner_reminder_at', '15:00');
        $this->assertIsArray($response->json('registry.types_by_category'));
    }

    public function test_update_persists_per_channel_per_type_toggles(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/settings/notification-preferences', [
            'email' => ['task_assigned' => false, 'kudos_received' => true],
            'push' => ['task_assigned' => true, 'kudos_received' => false],
        ]);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertFalse($user->wants('email', 'task_assigned'));
        $this->assertTrue($user->wants('email', 'kudos_received'));
        // push wants() requires a subscription before returning true; verify the
        // stored preference layer instead.
        $this->assertTrue($user->notification_preferences['push']['task_assigned']);
        $this->assertFalse($user->notification_preferences['push']['kudos_received']);
    }

    public function test_update_silently_drops_unknown_keys(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/settings/notification-preferences', [
            'email' => ['task_assigned' => true, 'definitely_not_a_real_type' => true],
        ]);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertArrayHasKey('task_assigned', $user->notification_preferences['email']);
        $this->assertArrayNotHasKey('definitely_not_a_real_type', $user->notification_preferences['email']);
    }

    public function test_quiet_hours_validation_rejects_bad_time_format(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->putJson('/api/v1/settings/notification-preferences', [
            'quiet_hours' => ['enabled' => true, 'start' => 'midnight', 'end' => '07:00'],
        ])->assertStatus(422);
    }
}
