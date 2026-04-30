<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDueSoonNotification;
use Illuminate\Console\Command;

class SendTaskDueReminders extends Command
{
    protected $signature = 'app:send-task-due-reminders';

    protected $description = 'Send a reminder to the assignee of every task that is due today.';

    public function handle(): int
    {
        $sent = 0;

        Task::query()
            ->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->whereNotNull('assigned_to')
            ->whereDate('due_date', today())
            ->whereNull('due_reminder_sent_at')
            ->with(['assignee.pushSubscriptions', 'family'])
            ->chunkById(200, function ($tasks) use (&$sent) {
                foreach ($tasks as $task) {
                    if (! $task->assignee) {
                        continue;
                    }

                    $task->assignee->notify(new TaskDueSoonNotification($task));
                    $task->update(['due_reminder_sent_at' => now()]);
                    $sent++;
                }
            });

        $this->info("Sent {$sent} task-due reminders.");

        return Command::SUCCESS;
    }
}
