<?php

namespace Tests\Unit\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PushSubscriptionCountCachingTest extends TestCase
{
    use RefreshDatabase;

    public function test_eager_loaded_relation_avoids_subscription_query(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email' => [],
                'push' => ['task_due_soon' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');

        // Reload with the eager-loaded relation — the scheduled-notification
        // commands all do this so they don't trigger an N+1 inside their loop.
        $reloaded = User::with('pushSubscriptions')->find($user->id);

        DB::enableQueryLog();
        $reloaded->wants('push', 'task_due_soon');
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $pushQueries = array_filter($queries, fn ($q) => str_contains($q['query'] ?? '', 'push_subscriptions'));
        $this->assertCount(0, $pushQueries, 'wants(push, ...) should honor an eager-loaded relation.');
    }

    public function test_eager_loaded_count_avoids_subscription_query(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'email' => [],
                'push' => ['task_due_soon' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');

        $reloaded = User::withCount('pushSubscriptions')->find($user->id);

        DB::enableQueryLog();
        $reloaded->wants('push', 'task_due_soon');
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $pushQueries = array_filter($queries, fn ($q) => str_contains($q['query'] ?? '', 'from "push_subscriptions"'));
        $this->assertCount(0, $pushQueries, 'wants(push, ...) should honor a loadCount-style eager count.');
    }
}
