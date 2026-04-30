<?php

namespace Tests\Feature\Notifications;

use App\Models\Family;
use App\Models\User;
use App\Notifications\KudosReceivedNotification;
use App\Services\PointsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use Tests\TestCase;

class KudosReceivedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_giving_kudos_notifies_recipient(): void
    {
        Notification::fake();

        $family = Family::factory()->create();
        $from = User::factory()->create(['family_id' => $family->id]);
        // Recipient opts in to email kudos so via() returns a channel — without
        // any enabled channels the fake silently drops the notification (see
        // NotificationFake::sendNow line 325).
        $to = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => ['kudos_received' => true],
                'push' => [],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);

        app(PointsService::class)->giveKudos($from, $to, $family, 'great job today');

        Notification::assertSentTo($to, KudosReceivedNotification::class, function ($notification) use ($from) {
            return $notification->from->is($from) && $notification->reason === 'great job today';
        });
    }

    public function test_self_kudos_does_not_notify(): void
    {
        Notification::fake();

        $family = Family::factory()->create();
        $user = User::factory()->create(['family_id' => $family->id]);

        app(PointsService::class)->giveKudos($user, $user, $family, 'self high-five');

        Notification::assertNothingSent();
    }

    public function test_via_includes_push_only_when_user_wants_it_and_has_subscription(): void
    {
        $from = User::factory()->create();
        $to = User::factory()->create([
            'notification_preferences' => [
                'email' => ['kudos_received' => false],
                'push' => ['kudos_received' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);

        $notification = new KudosReceivedNotification($from, 'nice');

        // No subscription yet → push not in via().
        $this->assertNotContains(WebPushChannel::class, $notification->via($to));

        $to->updatePushSubscription(
            endpoint: 'https://example.test/push',
            key: 'pk',
            token: 'auth',
        );
        $to->refresh();

        $this->assertContains(WebPushChannel::class, $notification->via($to));
    }

    public function test_global_mute_strips_push_from_via(): void
    {
        $from = User::factory()->create();
        $to = User::factory()->create([
            'notification_preferences' => [
                'email' => ['kudos_received' => false],
                'push' => ['kudos_received' => true],
                'muted' => true,
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
            ],
        ]);
        $to->updatePushSubscription(endpoint: 'https://example.test/push', key: 'pk', token: 'auth');
        $to->refresh();

        $notification = new KudosReceivedNotification($from, 'nice');
        $this->assertNotContains(WebPushChannel::class, $notification->via($to));
    }
}
