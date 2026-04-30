<?php

namespace App\Console\Commands;

use App\Models\EventReminderSend;
use App\Models\FamilyEvent;
use App\Notifications\CalendarEventReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SendCalendarEventReminders extends Command
{
    protected $signature = 'app:send-event-reminders';

    protected $description = 'Send reminders for upcoming calendar events whose lead-time window matches the current cron tick.';

    public function handle(): int
    {
        $now = now();
        $windowEnd = $now->copy()->addHour();

        // Center a 5-min match window roughly on the current tick. This catches
        // the firing time even if the cron drifts a couple of minutes.
        $tickWindowStart = $now->copy()->subMinutes(2);
        $tickWindowEnd = $now->copy()->addMinutes(3);

        $sent = 0;

        $candidates = FamilyEvent::query()
            ->where('is_active', true)
            ->whereNotNull('reminder_minutes_before')
            ->where(function ($q) use ($now, $windowEnd) {
                // Non-recurring: start_time falls inside the lookahead window
                $q->where(function ($qq) use ($now, $windowEnd) {
                    $qq->where(function ($qqq) {
                        $qqq->where('recurrence', 'none')->orWhereNull('recurrence');
                    })->whereBetween('start_time', [$now, $windowEnd]);
                })
                    // Recurring: cheap pre-filter, precise check happens in PHP
                    ->orWhereIn('recurrence', ['weekly', 'monthly', 'yearly']);
            })
            ->with('family.members.pushSubscriptions')
            ->get();

        foreach ($candidates as $event) {
            $occurrences = $event->occurrencesInRange($now, $windowEnd);

            foreach ($occurrences as $occurrence) {
                // Combine the occurrence date with the event's local time-of-day.
                $fireBaseTime = $event->start_time;
                $occurrenceAt = $occurrence->copy()
                    ->setTime($fireBaseTime->hour, $fireBaseTime->minute, $fireBaseTime->second);

                $fireAt = $occurrenceAt->copy()->subMinutes((int) $event->reminder_minutes_before);

                if (! $fireAt->between($tickWindowStart, $tickWindowEnd)) {
                    continue;
                }

                if (! $this->markSent($event, $occurrenceAt)) {
                    // Another worker / earlier tick already sent this occurrence
                    continue;
                }

                $recipients = $event->family?->members ?? collect();
                if ($recipients->isEmpty()) {
                    continue;
                }

                Notification::send($recipients, new CalendarEventReminderNotification($event, $occurrenceAt));
                $sent++;
            }
        }

        $this->info("Sent {$sent} calendar-event reminders.");

        return Command::SUCCESS;
    }

    /**
     * Insert the dedup row inside a transaction. Returns false if another worker
     * beat us to the unique constraint (in which case we skip dispatch).
     */
    private function markSent(FamilyEvent $event, Carbon $occurrenceAt): bool
    {
        try {
            DB::transaction(function () use ($event, $occurrenceAt) {
                EventReminderSend::create([
                    'family_event_id' => $event->id,
                    'occurrence_date' => $occurrenceAt->toDateString(),
                    'sent_at' => now(),
                ]);
            });

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
