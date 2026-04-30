<?php

namespace App\Console\Commands;

use App\Enums\MealSlot;
use App\Models\MealPlanEntry;
use App\Models\User;
use App\Notifications\DinnerReminderNotification;
use Illuminate\Console\Command;

class SendDinnerReminders extends Command
{
    protected $signature = 'app:send-dinner-reminders';

    protected $description = 'Send a daily push to users whose dinner-reminder time matches the current minute (in their local timezone).';

    public function handle(): int
    {
        $serverNowHHMM = now()->format('H:i');
        $sent = 0;

        // Coarse SQL pre-filter on the JSON column collapses 1000s of users
        // down to the handful whose preferred reminder time matches server-time
        // right now. The user's timezone may shift this — we re-check per-row.
        User::query()
            ->whereRaw("notification_preferences->>'dinner_reminder_at' = ?", [$serverNowHHMM])
            ->whereNotNull('family_id')
            ->with('pushSubscriptions')
            ->chunkById(200, function ($users) use (&$sent) {
                foreach ($users as $user) {
                    $localNow = now()->setTimezone($user->timezone ?: 'UTC')->format('H:i');
                    $preferred = $user->notification_preferences['dinner_reminder_at'] ?? '15:00';

                    if ($localNow !== $preferred) {
                        continue;
                    }

                    $entry = MealPlanEntry::query()
                        ->whereHas('mealPlan', fn ($q) => $q->where('family_id', $user->family_id))
                        ->whereDate('date', now()->setTimezone($user->timezone ?: 'UTC')->toDateString())
                        ->where('meal_slot', MealSlot::Dinner)
                        ->with(['recipe', 'restaurant', 'preset', 'mealPlan.family'])
                        ->first();

                    if (! $entry) {
                        continue;
                    }

                    $user->notify(new DinnerReminderNotification($entry));
                    $sent++;
                }
            });

        $this->info("Sent {$sent} dinner reminders.");

        return Command::SUCCESS;
    }
}
