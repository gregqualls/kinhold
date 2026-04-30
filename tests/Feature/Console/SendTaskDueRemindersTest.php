<?php

namespace Tests\Feature\Console;

use App\Models\Family;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskDueSoonNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendTaskDueRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_for_tasks_due_today_with_assignee(): void
    {
        Notification::fake();

        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => ['task_due_soon' => true],
                'push' => [],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);

        $task = Task::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'title' => 'Due today',
            'due_date' => now()->toDateString(),
        ]);

        $this->artisan('app:send-task-due-reminders')->assertSuccessful();

        Notification::assertSentTo($user, TaskDueSoonNotification::class);
        $this->assertNotNull($task->fresh()->due_reminder_sent_at);
    }

    public function test_skips_already_reminded_tasks(): void
    {
        Notification::fake();

        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => ['task_due_soon' => true],
                'push' => [],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);

        Task::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'title' => 'Already reminded',
            'due_date' => now()->toDateString(),
            'due_reminder_sent_at' => now()->subHour(),
        ]);

        $this->artisan('app:send-task-due-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_skips_completed_and_unassigned_tasks(): void
    {
        Notification::fake();

        $family = Family::factory()->create();
        $user = User::factory()->create([
            'family_id' => $family->id,
            'notification_preferences' => [
                'email' => ['task_due_soon' => true],
                'push' => [],
                'quiet_hours' => ['enabled' => false, 'start' => '22:00', 'end' => '07:00'],
                'muted' => false,
            ],
        ]);

        // Completed task
        Task::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'title' => 'Already done',
            'due_date' => now()->toDateString(),
            'completed_at' => now()->subHour(),
        ]);

        // Unassigned family task
        Task::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'assigned_to' => null,
            'is_family_task' => true,
            'title' => 'Family chore',
            'due_date' => now()->toDateString(),
        ]);

        $this->artisan('app:send-task-due-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }
}
