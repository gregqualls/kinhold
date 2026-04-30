<?php

namespace Tests\Feature\Notifications;

use App\Enums\MealSlot;
use App\Models\Family;
use App\Models\MealPlan;
use App\Models\MealPlanEntry;
use App\Models\Recipe;
use App\Models\User;
use App\Notifications\DinnerReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use NotificationChannels\WebPush\WebPushChannel;
use Tests\TestCase;

class DinnerReminderNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeDinnerEntry(Family $family, User $user): MealPlanEntry
    {
        $recipe = Recipe::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'title' => 'Tacos',
        ]);
        $plan = MealPlan::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'week_start' => now()->startOfWeek()->toDateString(),
        ]);

        return MealPlanEntry::create([
            'meal_plan_id' => $plan->id,
            'recipe_id' => $recipe->id,
            'date' => now()->toDateString(),
            'meal_slot' => MealSlot::Dinner,
        ]);
    }

    public function test_via_returns_push_when_user_opted_in_and_subscribed(): void
    {
        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => [],
                'push' => ['dinner_reminder' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');
        $user->refresh();

        $entry = $this->makeDinnerEntry($family, $user);

        $this->assertSame([WebPushChannel::class], (new DinnerReminderNotification($entry))->via($user));
    }

    public function test_via_returns_empty_when_user_opted_out(): void
    {
        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => [],
                'push' => ['dinner_reminder' => false],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');
        $user->refresh();

        $entry = $this->makeDinnerEntry($family, $user);

        $this->assertSame([], (new DinnerReminderNotification($entry))->via($user));
    }

    public function test_via_only_supports_push_channel(): void
    {
        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                // Email is irrelevant — registry says channels: ['push'].
                'email' => ['dinner_reminder' => true],
                'push' => ['dinner_reminder' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');
        $user->refresh();

        $entry = $this->makeDinnerEntry($family, $user);

        $this->assertSame([WebPushChannel::class], (new DinnerReminderNotification($entry))->via($user));
    }
}
