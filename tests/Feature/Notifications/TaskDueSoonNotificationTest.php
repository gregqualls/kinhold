<?php

namespace Tests\Feature\Notifications;

use App\Models\Family;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskDueSoonNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use NotificationChannels\WebPush\WebPushChannel;
use Tests\TestCase;

class TaskDueSoonNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_via_returns_both_channels_when_user_wants_both(): void
    {
        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => ['task_due_soon' => true],
                'push' => ['task_due_soon' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');
        $user->refresh();

        $task = Task::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'title' => 'Walk the dog',
            'due_date' => now()->toDateString(),
        ]);

        $notification = new TaskDueSoonNotification($task);
        $channels = $notification->via($user);

        $this->assertContains('mail', $channels);
        $this->assertContains(WebPushChannel::class, $channels);
    }

    public function test_via_strips_push_when_user_has_no_subscription(): void
    {
        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => ['task_due_soon' => false],
                'push' => ['task_due_soon' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);
        $task = Task::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'title' => 'No subscription task',
            'due_date' => now()->toDateString(),
        ]);

        $this->assertNotContains(WebPushChannel::class, (new TaskDueSoonNotification($task))->via($user));
    }

    public function test_via_strips_push_during_quiet_hours(): void
    {
        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'timezone' => 'UTC',
            'notification_preferences' => [
                'email' => [],
                'push' => ['task_due_soon' => true],
                // Always-on quiet hours window
                'quiet_hours' => ['enabled' => true, 'start' => '00:00', 'end' => '23:59'],
                'muted' => false,
            ],
        ]);
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');
        $user->refresh();

        $task = Task::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'title' => 'Quiet hours task',
            'due_date' => now()->toDateString(),
        ]);

        $this->assertNotContains(WebPushChannel::class, (new TaskDueSoonNotification($task))->via($user));
    }

    public function test_via_returns_empty_when_user_opted_out_of_both(): void
    {
        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => ['task_due_soon' => false],
                'push' => ['task_due_soon' => false],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);
        $task = Task::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'title' => 'Opted-out task',
            'due_date' => now()->toDateString(),
        ]);

        $this->assertSame([], (new TaskDueSoonNotification($task))->via($user));
    }
}
