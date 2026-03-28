<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRecurringTasks extends Command
{
    protected $signature = 'app:generate-recurring-tasks {--days=7 : How many days ahead to generate}';
    protected $description = 'Generate task instances from recurring task templates for the upcoming period';

    public function handle(): int
    {
        $daysAhead = (int) $this->option('days');
        $today = Carbon::today();
        $endDate = $today->copy()->addDays($daysAhead);

        $templates = Task::templates()
            ->where(function ($q) use ($today) {
                $q->whereNull('recurrence_end')
                    ->orWhere('recurrence_end', '>=', $today);
            })
            ->get();

        $created = 0;

        foreach ($templates as $template) {
            // Skip if there's already an incomplete instance of this template
            $hasPending = Task::where('parent_task_id', $template->id)
                ->whereNull('completed_at')
                ->exists();

            if ($hasPending) {
                continue;
            }

            $dates = $this->getOccurrenceDates($template->recurrence_rule, $today, $endDate);

            foreach ($dates as $date) {
                // Check if an instance already exists for this date
                $exists = Task::where('parent_task_id', $template->id)
                    ->whereDate('due_date', $date)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Check recurrence end
                if ($template->recurrence_end && $date->gt($template->recurrence_end)) {
                    continue;
                }

                Task::create([
                    'family_id' => $template->family_id,
                    'created_by' => $template->created_by,
                    'assigned_to' => $template->assigned_to,
                    'title' => $template->title,
                    'description' => $template->description,
                    'due_date' => $date,
                    'priority' => $template->priority,
                    'is_family_task' => $template->is_family_task,
                    'points' => $template->points,
                    'parent_task_id' => $template->id,
                ]);

                $created++;
            }
        }

        $this->info("Generated {$created} recurring task instance(s).");

        return Command::SUCCESS;
    }

    /**
     * Parse a simplified RRULE and return matching dates in the range.
     */
    private function getOccurrenceDates(string $rrule, Carbon $start, Carbon $end): array
    {
        $parts = [];
        foreach (explode(';', $rrule) as $segment) {
            [$key, $value] = explode('=', $segment, 2);
            $parts[strtoupper($key)] = $value;
        }

        $freq = $parts['FREQ'] ?? null;
        $dates = [];

        if ($freq === 'DAILY') {
            $current = $start->copy();
            while ($current->lte($end)) {
                $dates[] = $current->copy();
                $current->addDay();
            }
        } elseif ($freq === 'WEEKLY') {
            $dayMap = [
                'SU' => Carbon::SUNDAY,
                'MO' => Carbon::MONDAY,
                'TU' => Carbon::TUESDAY,
                'WE' => Carbon::WEDNESDAY,
                'TH' => Carbon::THURSDAY,
                'FR' => Carbon::FRIDAY,
                'SA' => Carbon::SATURDAY,
            ];

            $byDay = isset($parts['BYDAY']) ? explode(',', $parts['BYDAY']) : [];

            if (empty($byDay)) {
                // Default to same day of week as start
                $byDay = [array_search($start->dayOfWeek, $dayMap) ?: 'MO'];
            }

            foreach ($byDay as $day) {
                $dayNum = $dayMap[strtoupper(trim($day))] ?? null;
                if ($dayNum === null) continue;

                $current = $start->copy()->next($dayNum);
                if ($current->lt($start)) $current->addWeek();

                // Also check if start itself is the target day
                if ($start->dayOfWeek === $dayNum) {
                    $dates[] = $start->copy();
                }

                while ($current->lte($end)) {
                    $dates[] = $current->copy();
                    $current->addWeek();
                }
            }
        } elseif ($freq === 'MONTHLY') {
            $byMonthDay = isset($parts['BYMONTHDAY']) ? (int) $parts['BYMONTHDAY'] : $start->day;

            $current = $start->copy()->day(min($byMonthDay, $start->daysInMonth));
            if ($current->lt($start)) {
                $current->addMonth();
                $current->day(min($byMonthDay, $current->daysInMonth));
            }

            while ($current->lte($end)) {
                $dates[] = $current->copy();
                $current->addMonth();
                $current->day(min($byMonthDay, $current->daysInMonth));
            }
        }

        // Deduplicate and sort
        $unique = [];
        foreach ($dates as $d) {
            $key = $d->toDateString();
            if (!isset($unique[$key])) {
                $unique[$key] = $d;
            }
        }

        sort($unique);
        return array_values($unique);
    }
}
