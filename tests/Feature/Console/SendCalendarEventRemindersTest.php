<?php

namespace Tests\Feature\Console;

use App\Models\EventReminderSend;
use App\Models\Family;
use App\Models\FamilyEvent;
use App\Models\User;
use App\Notifications\CalendarEventReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendCalendarEventRemindersTest extends TestCase
{
    use RefreshDatabase;

    private function familyWithMember(): array
    {
        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => ['calendar_event_reminder' => true],
                'push' => [],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);

        return [$family, $user];
    }

    public function test_fires_one_time_event_inside_window(): void
    {
        Notification::fake();
        Carbon::setTestNow('2026-05-01 10:00:00');

        [$family, $user] = $this->familyWithMember();

        // Event starts at 10:30, reminder 30 min before → fires at 10:00 (now).
        FamilyEvent::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'title' => 'Soccer',
            'start_time' => '2026-05-01 10:30:00',
            'reminder_minutes_before' => 30,
            'recurrence' => 'none',
            'is_active' => true,
        ]);

        $this->artisan('app:send-event-reminders')->assertSuccessful();

        Notification::assertSentTo($user, CalendarEventReminderNotification::class);
        $this->assertSame(1, EventReminderSend::count());

        Carbon::setTestNow();
    }

    public function test_fires_recurring_weekly_occurrence(): void
    {
        Notification::fake();
        Carbon::setTestNow('2026-05-08 10:00:00');

        [$family, $user] = $this->familyWithMember();

        // Original event was a week ago — weekly recurrence puts the next
        // occurrence today at 10:30 (30 min lead → fires now).
        FamilyEvent::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'title' => 'Weekly soccer',
            'start_time' => '2026-05-01 10:30:00',
            'reminder_minutes_before' => 30,
            'recurrence' => 'weekly',
            'is_active' => true,
        ]);

        $this->artisan('app:send-event-reminders')->assertSuccessful();

        Notification::assertSentTo($user, CalendarEventReminderNotification::class);

        Carbon::setTestNow();
    }

    public function test_unique_constraint_prevents_double_fire_on_same_occurrence(): void
    {
        Notification::fake();
        Carbon::setTestNow('2026-05-01 10:00:00');

        [$family, $user] = $this->familyWithMember();

        FamilyEvent::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'title' => 'Soccer',
            'start_time' => '2026-05-01 10:30:00',
            'reminder_minutes_before' => 30,
            'recurrence' => 'none',
            'is_active' => true,
        ]);

        $this->artisan('app:send-event-reminders')->assertSuccessful();
        $this->artisan('app:send-event-reminders')->assertSuccessful();

        Notification::assertSentToTimes($user, CalendarEventReminderNotification::class, 1);
        $this->assertSame(1, EventReminderSend::count());

        Carbon::setTestNow();
    }

    public function test_skips_event_with_null_reminder_minutes_before(): void
    {
        Notification::fake();
        Carbon::setTestNow('2026-05-01 10:00:00');

        [$family, $user] = $this->familyWithMember();

        FamilyEvent::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'title' => 'No reminder',
            'start_time' => '2026-05-01 10:30:00',
            'reminder_minutes_before' => null,
            'recurrence' => 'none',
            'is_active' => true,
        ]);

        $this->artisan('app:send-event-reminders')->assertSuccessful();

        Notification::assertNothingSent();

        Carbon::setTestNow();
    }
}
