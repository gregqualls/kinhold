<?php

namespace Tests\Feature\Notifications;

use App\Models\Family;
use App\Models\FamilyEvent;
use App\Models\User;
use App\Notifications\CalendarEventReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use NotificationChannels\WebPush\WebPushChannel;
use Tests\TestCase;

class CalendarEventReminderNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_via_returns_both_channels_when_opted_in(): void
    {
        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => ['calendar_event_reminder' => true],
                'push' => ['calendar_event_reminder' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');
        $user->refresh();

        $event = FamilyEvent::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'title' => 'Soccer game',
            'start_time' => now()->addMinutes(30),
            'reminder_minutes_before' => 30,
            'is_active' => true,
        ]);

        $channels = (new CalendarEventReminderNotification($event, $event->start_time))->via($user);

        $this->assertContains('mail', $channels);
        $this->assertContains(WebPushChannel::class, $channels);
    }

    public function test_via_skips_user_who_opted_out(): void
    {
        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => ['calendar_event_reminder' => false],
                'push' => ['calendar_event_reminder' => false],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);

        $event = FamilyEvent::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'title' => 'Soccer game',
            'start_time' => now()->addMinutes(30),
            'reminder_minutes_before' => 30,
            'is_active' => true,
        ]);

        $this->assertSame([], (new CalendarEventReminderNotification($event, $event->start_time))->via($user));
    }

    public function test_via_strips_push_when_globally_muted(): void
    {
        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => [],
                'push' => ['calendar_event_reminder' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => true,
            ],
        ]);
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');
        $user->refresh();

        $event = FamilyEvent::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'title' => 'Soccer game',
            'start_time' => now()->addMinutes(30),
            'reminder_minutes_before' => 30,
            'is_active' => true,
        ]);

        $this->assertNotContains(WebPushChannel::class, (new CalendarEventReminderNotification($event, $event->start_time))->via($user));
    }
}
