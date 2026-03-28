<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskCompletedNotification;
use App\Services\BadgeService;
use App\Services\PointsService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('complete-task')]
#[Description('Mark a task as complete or incomplete. Completing a task awards points and may trigger badge achievements. Actions: complete, uncomplete.')]
class CompleteTask extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['complete', 'uncomplete'])->description('Whether to complete or uncomplete the task'),
            'task_id' => $schema->string()->required()->description('UUID of the task'),
        ];
    }

    public function handle(Request $request): Response
    {
        $task = Task::where('family_id', $this->familyId())
            ->findOrFail($request->get('task_id'));

        $user = $this->user();
        $pointsService = app(PointsService::class);
        $badgeService = app(BadgeService::class);

        if ($request->get('action') === 'complete') {
            if ($task->isComplete()) {
                return Response::error("Task \"{$task->title}\" is already completed.");
            }

            $task->update(['completed_at' => now()]);

            // Children can't earn points from tasks they created themselves
            $skipPoints = !$user->isParent() && $task->created_by === $user->id;
            $transaction = $skipPoints
                ? $pointsService->awardTaskPoints($task, $user, forceZero: true)
                : $pointsService->awardTaskPoints($task, $user);

            $newBadges = $badgeService->checkAndAwardBadges($user);

            // Notify family
            $family = $this->family();
            $family->members()->where('id', '!=', $user->id)->get()
                ->each(fn (User $m) => $m->notify(new TaskCompletedNotification($task, $user)));

            $result = [
                'message' => "Task \"{$task->title}\" completed! +{$transaction->points} points.",
                'points_earned' => $transaction->points,
            ];

            if (!empty($newBadges)) {
                $result['badges_earned'] = collect($newBadges)->map(fn ($b) => $b->name)->toArray();
            }

            return Response::json($result);
        }

        // Uncomplete
        if (!$task->isComplete()) {
            return Response::error("Task \"{$task->title}\" is not completed.");
        }

        $task->update(['completed_at' => null]);
        $pointsService->reverseTaskPoints($task, $user);

        return Response::json([
            'message' => "Task \"{$task->title}\" marked incomplete. Points reversed.",
        ]);
    }
}
