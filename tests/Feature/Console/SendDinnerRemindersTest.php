<?php

namespace Tests\Feature\Console;

use App\Enums\MealSlot;
use App\Models\Family;
use App\Models\MealPlan;
use App\Models\MealPlanEntry;
use App\Models\Recipe;
use App\Models\User;
use App\Notifications\DinnerReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendDinnerRemindersTest extends TestCase
{
    use RefreshDatabase;

    private function planTodayDinner(Family $family, User $user): MealPlanEntry
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

    public function test_dispatches_when_local_time_matches_dinner_reminder_at(): void
    {
        Notification::fake();
        Carbon::setTestNow('2026-05-01 17:00:00');

        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'timezone' => 'UTC',
            'notification_preferences' => [
                'email' => [],
                'push' => ['dinner_reminder' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
                'dinner_reminder_at' => '17:00',
            ],
        ]);
        $user->updatePushSubscription(endpoint: 'https://example.test/p', key: 'pk', token: 'auth');

        $this->planTodayDinner($family, $user);

        $this->artisan('app:send-dinner-reminders')->assertSuccessful();

        Notification::assertSentTo($user, DinnerReminderNotification::class);

        Carbon::setTestNow();
    }

    public function test_skips_users_whose_local_time_does_not_match(): void
    {
        Notification::fake();
        Carbon::setTestNow('2026-05-01 17:00:00');

        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'timezone' => 'UTC',
            'notification_preferences' => [
                'email' => [],
                'push' => ['dinner_reminder' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
                'dinner_reminder_at' => '15:00',
            ],
        ]);

        $this->planTodayDinner($family, $user);

        $this->artisan('app:send-dinner-reminders')->assertSuccessful();

        Notification::assertNothingSent();

        Carbon::setTestNow();
    }

    public function test_skips_users_with_no_dinner_planned(): void
    {
        Notification::fake();
        Carbon::setTestNow('2026-05-01 17:00:00');

        $family = Family::factory()->create();
        User::factory()->create([
            'family_id' => $family->id,
            'timezone' => 'UTC',
            'notification_preferences' => [
                'email' => [],
                'push' => ['dinner_reminder' => true],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
                'dinner_reminder_at' => '17:00',
            ],
        ]);

        // No MealPlanEntry for today.

        $this->artisan('app:send-dinner-reminders')->assertSuccessful();

        Notification::assertNothingSent();

        Carbon::setTestNow();
    }
}
