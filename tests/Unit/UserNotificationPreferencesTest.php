<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserNotificationPreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_wants_email_falls_back_to_registry_default(): void
    {
        $user = User::factory()->create(['email_preferences' => null, 'notification_preferences' => null]);

        // Per registry: task_assigned default_email = true.
        $this->assertTrue($user->wants('email', 'task_assigned'));
        // Per registry: kudos_received default_email = false.
        $this->assertFalse($user->wants('email', 'kudos_received'));
    }

    public function test_wants_email_returns_false_for_users_with_no_email(): void
    {
        $user = User::factory()->create(['email' => null, 'notification_preferences' => [
            'email' => ['task_assigned' => true],
        ]]);

        $this->assertFalse($user->wants('email', 'task_assigned'));
    }

    public function test_wants_push_requires_active_subscription(): void
    {
        $user = User::factory()->create(['notification_preferences' => [
            'email' => [],
            'push' => ['task_assigned' => true],
            'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
            'muted' => false,
        ]]);

        // No subscription registered → push channel is unaddressable.
        $this->assertFalse($user->wants('push', 'task_assigned'));

        $user->updatePushSubscription(
            endpoint: 'https://example.test/push',
            key: 'pk',
            token: 'auth',
        );

        $this->assertTrue($user->refresh()->wants('push', 'task_assigned'));
    }

    public function test_wantsemail_legacy_helper_strips_email_prefix(): void
    {
        $user = User::factory()->create(['notification_preferences' => [
            'email' => ['task_assigned' => false],
        ]]);

        $this->assertFalse($user->wantsEmail('email_task_assigned'));
        $this->assertFalse($user->wantsEmail('task_assigned'));
    }

    public function test_quiet_hours_same_day_window(): void
    {
        $user = User::factory()->create([
            'timezone' => 'UTC',
            'notification_preferences' => [
                'quiet_hours' => ['enabled' => true, 'start' => '13:00', 'end' => '17:00'],
            ],
        ]);

        $this->assertTrue($user->isInQuietHours(Carbon::parse('2026-04-30 14:30:00', 'UTC')));
        $this->assertFalse($user->isInQuietHours(Carbon::parse('2026-04-30 12:00:00', 'UTC')));
        $this->assertFalse($user->isInQuietHours(Carbon::parse('2026-04-30 17:00:00', 'UTC')));
    }

    public function test_quiet_hours_overnight_window(): void
    {
        $user = User::factory()->create([
            'timezone' => 'UTC',
            'notification_preferences' => [
                'quiet_hours' => ['enabled' => true, 'start' => '22:00', 'end' => '07:00'],
            ],
        ]);

        $this->assertTrue($user->isInQuietHours(Carbon::parse('2026-04-30 23:00:00', 'UTC')));
        $this->assertTrue($user->isInQuietHours(Carbon::parse('2026-04-30 06:30:00', 'UTC')));
        $this->assertFalse($user->isInQuietHours(Carbon::parse('2026-04-30 12:00:00', 'UTC')));
    }

    public function test_quiet_hours_respects_user_timezone(): void
    {
        // 22:00–07:00 in America/New_York. UTC equivalent: 02:00–11:00 UTC (during EST/EDT).
        $user = User::factory()->create([
            'timezone' => 'America/New_York',
            'notification_preferences' => [
                'quiet_hours' => ['enabled' => true, 'start' => '22:00', 'end' => '07:00'],
            ],
        ]);

        // 03:00 UTC == 23:00 NY (EDT) — quiet.
        $this->assertTrue($user->isInQuietHours(Carbon::parse('2026-04-30 03:00:00', 'UTC')));
        // 16:00 UTC == 12:00 NY (EDT) — not quiet.
        $this->assertFalse($user->isInQuietHours(Carbon::parse('2026-04-30 16:00:00', 'UTC')));
    }

    public function test_global_mute_short_circuits_push_suppression(): void
    {
        $user = User::factory()->make([
            'notification_preferences' => ['muted' => true],
        ]);

        $this->assertTrue($user->isPushMuted());
        $this->assertTrue($user->isPushSuppressed());
    }
}
