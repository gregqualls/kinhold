<?php

namespace App\Console\Commands;

use App\Models\Family;
use App\Models\PointTransaction;
use App\Models\Task;
use App\Models\User;
use App\Notifications\WeeklyDigestNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendWeeklyDigest extends Command
{
    protected $signature = 'app:send-weekly-digest';

    protected $description = 'Send weekly activity digest emails to all family members who have opted in';

    public function handle(): int
    {
        $weekStart = Carbon::now()->subWeek()->startOfWeek();
        $weekEnd = Carbon::now()->subWeek()->endOfWeek();
        $weekRange = $weekStart->format('M j').' - '.$weekEnd->format('M j, Y');

        $nextWeekStart = Carbon::now()->startOfWeek();
        $nextWeekEnd = Carbon::now()->endOfWeek();

        $families = Family::with('members')->get();
        $sent = 0;

        foreach ($families as $family) {
            foreach ($family->members as $member) {
                // Skip managed accounts without email
                if (! $member->email) {
                    continue;
                }

                // Skip users who opted out
                if (! $member->wantsEmail('email_weekly_digest')) {
                    continue;
                }

                $digest = $this->buildDigest($family, $member, $weekStart, $weekEnd, $nextWeekStart, $nextWeekEnd, $weekRange);

                $member->notify(new WeeklyDigestNotification($digest));
                $sent++;
            }
        }

        $this->info("Sent {$sent} weekly digest emails.");

        return Command::SUCCESS;
    }

    /**
     * Build the digest data for a specific user.
     */
    private function buildDigest(
        Family $family,
        User $member,
        Carbon $weekStart,
        Carbon $weekEnd,
        Carbon $nextWeekStart,
        Carbon $nextWeekEnd,
        string $weekRange,
    ): array {
        // Tasks completed this week (by anyone in the family)
        $tasksCompleted = Task::where('family_id', $family->id)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$weekStart, $weekEnd])
            ->count();

        // Pending tasks
        $tasksPending = Task::where('family_id', $family->id)
            ->whereNull('completed_at')
            ->whereNull('parent_task_id')
            ->count();

        // Overdue tasks
        $tasksOverdue = Task::where('family_id', $family->id)
            ->overdue()
            ->count();

        // Points earned this week by this user
        $pointsEarned = PointTransaction::where('user_id', $member->id)
            ->where('points', '>', 0)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->sum('points');

        // Current point bank
        $pointsBank = $member->pointBank();

        // Upcoming tasks next week (assigned to this user or family tasks)
        $upcomingTasks = Task::where('family_id', $family->id)
            ->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$nextWeekStart, $nextWeekEnd])
            ->where(function ($q) use ($member) {
                $q->where('assigned_to', $member->id)
                    ->orWhere('is_family_task', true)
                    ->orWhereNull('assigned_to');
            })
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(fn (Task $t) => [
                'title' => $t->title,
                'due_date' => $t->due_date?->format('M j'),
            ])
            ->toArray();

        // Badges earned this week
        $badgesEarned = $member->badges()
            ->wherePivotBetween('earned_at', [$weekStart, $weekEnd])
            ->get()
            ->map(fn ($b) => [
                'name' => $b->name,
                'description' => $b->description,
            ])
            ->toArray();

        return [
            'family_name' => $family->name,
            'week_range' => $weekRange,
            'tasks_completed' => $tasksCompleted,
            'tasks_pending' => $tasksPending,
            'tasks_overdue' => $tasksOverdue,
            'points_earned' => (int) $pointsEarned,
            'points_bank' => $pointsBank,
            'upcoming_tasks' => $upcomingTasks,
            'badges_earned' => $badgesEarned,
        ];
    }
}
