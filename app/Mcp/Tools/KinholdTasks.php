<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\RequiresModule;
use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskCompletedNotification;
use App\Services\BadgeService;
use App\Services\PointsService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('kinhold-tasks')]
#[Description(<<<'DESC'
Tasks, completion (with points + badge side effects), and tags.

Tasks:
  task_list (status?, assigned_to?) — Family tasks ordered by due date.
  task_create (title*, description?, assigned_to?, due_date?, priority?, is_family_task?, points?, tag_ids?) — Children cannot set custom points.
  task_update (task_id*, [any field]) — Subject to policy.
  task_delete (task_id*) — Subject to policy.
  task_complete (task_id*) — Awards points + checks badges. Children get 0 points for tasks they created themselves. Notifies family.
  task_uncomplete (task_id*) — Reverses points.

Tags (scoped to either tasks or food):
  tag_list (scope?) — All family tags. Filter by scope: task, food.
  tag_create (name*, scope?, color?) — Default scope: task.
  tag_delete (tag_id*).

Priorities: low, medium, high. Status filter: pending, completed.
DESC)]
class KinholdTasks extends Tool
{
    use RequiresModule, ScopesToFamily;

    public const MODULE = 'tasks';

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum([
                'task_list', 'task_create', 'task_update', 'task_delete',
                'task_complete', 'task_uncomplete',
                'tag_list', 'tag_create', 'tag_delete',
            ])->description('Action to perform'),
            'task_id' => $schema->string()->description('Task UUID (required for task_update/task_delete/task_complete/task_uncomplete)'),
            'tag_id' => $schema->string()->description('Tag UUID (required for tag_delete)'),
            'title' => $schema->string()->description('Task title (required for task_create)'),
            'description' => $schema->string()->description('Task description'),
            'assigned_to' => $schema->string()->description('UUID of family member to assign'),
            'due_date' => $schema->string()->description('Due date YYYY-MM-DD'),
            'priority' => $schema->string()->enum(['low', 'medium', 'high'])->description('Task priority'),
            'is_family_task' => $schema->boolean()->description('Any family member can complete'),
            'points' => $schema->integer()->description('Custom points (parent only)'),
            'status' => $schema->string()->enum(['pending', 'completed'])->description('Filter for task_list'),
            'tag_ids' => $schema->array()->items($schema->string())->description('Tag UUIDs to attach'),
            'name' => $schema->string()->description('Tag name (required for tag_create)'),
            'color' => $schema->string()->description('Hex color for tag (e.g. #FF5733)'),
            'scope' => $schema->string()->enum(['task', 'food'])->description('Tag scope. Default for tag_create: task. Acts as filter on tag_list.'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'task_list' => $this->taskList($request),
            'task_create' => $this->taskCreate($request),
            'task_update' => $this->taskUpdate($request),
            'task_delete' => $this->taskDelete($request),
            'task_complete' => $this->taskComplete($request),
            'task_uncomplete' => $this->taskUncomplete($request),
            'tag_list' => $this->tagList($request),
            'tag_create' => $this->tagCreate($request),
            'tag_delete' => $this->tagDelete($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function taskList(Request $request): Response
    {
        $query = Task::where('family_id', $this->familyId());

        if ($assignedTo = $request->get('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }
        if ($status = $request->get('status')) {
            $status === 'completed'
                ? $query->whereNotNull('completed_at')
                : $query->whereNull('completed_at');
        }

        $tasks = $query->with(['assignee', 'tags'])
            ->orderByRaw('completed_at IS NOT NULL')
            ->orderBy('due_date')
            ->limit(50)
            ->get();

        return Response::json([
            'tasks' => $tasks->map(fn ($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'description' => $t->description,
                'status' => $t->isComplete() ? 'completed' : 'pending',
                'priority' => $t->priority?->value ?? $t->priority,
                'due_date' => $t->due_date?->format('Y-m-d'),
                'assigned_to' => $t->assignee?->name,
                'assigned_to_id' => $t->assigned_to,
                'is_family_task' => $t->is_family_task,
                'points' => $t->getEffectivePoints(),
                'tags' => $t->tags->pluck('name')->toArray(),
                'is_recurring' => $t->isRecurring(),
            ])->toArray(),
        ]);
    }

    private function taskCreate(Request $request): Response
    {
        $title = $request->get('title');
        if (! $title) {
            return Response::error('title is required to create a task.');
        }

        $family = $this->family();
        $user = $this->user();

        $assignedTo = $request->get('assigned_to');
        if ($assignedTo) {
            $family->members()->findOrFail($assignedTo);
            if ($assignedTo !== $user->id && ! $family->userCanAssignTasks($user)) {
                $assignedTo = $user->id;
            }
        }

        $points = $request->get('points');
        if (! $user->isParent()) {
            $points = null;
        }

        $task = Task::create([
            'family_id' => $family->id,
            'created_by' => $user->id,
            'title' => $title,
            'description' => $request->get('description'),
            'assigned_to' => $assignedTo,
            'due_date' => $request->get('due_date'),
            'priority' => $request->get('priority', 'medium'),
            'is_family_task' => $request->get('is_family_task', false),
            'points' => $points,
            'sort_order' => Task::where('family_id', $family->id)->max('sort_order') + 1,
        ]);

        if ($tagIds = $request->get('tag_ids')) {
            $task->tags()->sync($tagIds);
        }

        if ($task->assigned_to && $task->assigned_to !== $user->id) {
            $assignee = User::find($task->assigned_to);
            $assignee?->notify(new TaskAssignedNotification($task, $user));
        }

        return Response::json([
            'message' => "Task \"{$task->title}\" created.",
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'assigned_to' => $task->assignee?->name ?? null,
                'due_date' => $task->due_date?->format('Y-m-d'),
                'priority' => $task->priority?->value ?? $task->priority,
                'points' => $task->getEffectivePoints(),
            ],
        ]);
    }

    private function taskUpdate(Request $request): Response
    {
        $taskId = $request->get('task_id');
        if (! $taskId) {
            return Response::error('task_id is required for task_update.');
        }

        $task = Task::where('family_id', $this->familyId())->findOrFail($taskId);

        if ($denied = $this->authorize('update', $task)) {
            return $denied;
        }

        $user = $this->user();

        $updates = [];
        foreach (['title', 'description', 'due_date', 'priority', 'is_family_task'] as $field) {
            if ($request->get($field) !== null) {
                $updates[$field] = $request->get($field);
            }
        }

        if ($request->get('assigned_to') !== null) {
            $assignedTo = $request->get('assigned_to');
            $this->family()->members()->findOrFail($assignedTo);
            $updates['assigned_to'] = $assignedTo;
        }

        if ($request->get('points') !== null && $user->isParent()) {
            $updates['points'] = $request->get('points');
        }

        $task->update($updates);

        if ($tagIds = $request->get('tag_ids')) {
            $task->tags()->sync($tagIds);
        }

        return Response::json([
            'message' => "Task \"{$task->title}\" updated.",
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'due_date' => $task->due_date?->format('Y-m-d'),
                'priority' => $task->priority?->value ?? $task->priority,
            ],
        ]);
    }

    private function taskDelete(Request $request): Response
    {
        $taskId = $request->get('task_id');
        if (! $taskId) {
            return Response::error('task_id is required for task_delete.');
        }

        $task = Task::where('family_id', $this->familyId())->findOrFail($taskId);

        if ($denied = $this->authorize('delete', $task)) {
            return $denied;
        }

        $title = $task->title;
        $task->delete();

        return Response::text("Task \"{$title}\" deleted.");
    }

    private function taskComplete(Request $request): Response
    {
        $taskId = $request->get('task_id');
        if (! $taskId) {
            return Response::error('task_id is required for task_complete.');
        }

        $task = Task::where('family_id', $this->familyId())->findOrFail($taskId);

        if ($denied = $this->authorize('complete', $task)) {
            return $denied;
        }

        if ($task->isComplete()) {
            return Response::error("Task \"{$task->title}\" is already completed.");
        }

        $user = $this->user();
        $pointsService = app(PointsService::class);
        $badgeService = app(BadgeService::class);

        $task->update(['completed_at' => now()]);

        $skipPoints = ! $user->isParent() && $task->created_by === $user->id;
        $transaction = $skipPoints
            ? $pointsService->awardTaskPoints($task, $user, forceZero: true)
            : $pointsService->awardTaskPoints($task, $user);

        $newBadges = $badgeService->checkAndAwardBadges($user);

        $family = $this->family();
        $family->members()->where('id', '!=', $user->id)->get()
            ->each(fn (User $m) => $m->notify(new TaskCompletedNotification($task, $user)));

        $result = [
            'message' => "Task \"{$task->title}\" completed! +{$transaction->points} points.",
            'points_earned' => $transaction->points,
        ];

        if (! empty($newBadges)) {
            $result['badges_earned'] = collect($newBadges)->map(fn ($b) => $b->name)->toArray();
        }

        return Response::json($result);
    }

    private function taskUncomplete(Request $request): Response
    {
        $taskId = $request->get('task_id');
        if (! $taskId) {
            return Response::error('task_id is required for task_uncomplete.');
        }

        $task = Task::where('family_id', $this->familyId())->findOrFail($taskId);

        if ($denied = $this->authorize('complete', $task)) {
            return $denied;
        }

        if (! $task->isComplete()) {
            return Response::error("Task \"{$task->title}\" is not completed.");
        }

        $user = $this->user();
        $pointsService = app(PointsService::class);

        $task->update(['completed_at' => null]);
        $pointsService->reverseTaskPoints($task, $user);

        return Response::json([
            'message' => "Task \"{$task->title}\" marked incomplete. Points reversed.",
        ]);
    }

    private function tagList(Request $request): Response
    {
        $query = Tag::where('family_id', $this->familyId())
            ->withCount('tasks')
            ->withCount('recipes')
            ->withCount('restaurants')
            ->orderBy('sort_order');

        if ($scope = $request->get('scope')) {
            if (! in_array($scope, ['task', 'food'], true)) {
                return Response::error('scope must be "task" or "food".');
            }
            $query->where('scope', $scope);
        }

        return Response::json([
            'tags' => $query->get()->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'color' => $t->color,
                'scope' => $t->scope->value,
                'task_count' => $t->tasks_count,
                'recipe_count' => $t->recipes_count,
                'restaurant_count' => $t->restaurants_count,
            ])->toArray(),
        ]);
    }

    private function tagCreate(Request $request): Response
    {
        if ($denied = $this->authorize('create', Tag::class)) {
            return $denied;
        }

        $name = $request->get('name');
        if (! $name) {
            return Response::error('name is required to create a tag.');
        }

        $scope = $request->get('scope', 'task');
        if (! in_array($scope, ['task', 'food'], true)) {
            return Response::error('scope must be "task" or "food".');
        }

        $tag = Tag::create([
            'family_id' => $this->familyId(),
            'name' => $name,
            'color' => $request->get('color'),
            'scope' => $scope,
            'sort_order' => Tag::where('family_id', $this->familyId())->max('sort_order') + 1,
        ]);

        return Response::json([
            'message' => "Tag \"{$tag->name}\" created with scope \"{$scope}\".",
            'tag' => ['id' => $tag->id, 'name' => $tag->name, 'color' => $tag->color, 'scope' => $scope],
        ]);
    }

    private function tagDelete(Request $request): Response
    {
        $tagId = $request->get('tag_id');
        if (! $tagId) {
            return Response::error('tag_id is required for tag_delete.');
        }

        $tag = Tag::where('family_id', $this->familyId())->findOrFail($tagId);

        if ($denied = $this->authorize('delete', $tag)) {
            return $denied;
        }
        $name = $tag->name;
        $tag->delete();

        return Response::text("Tag \"{$name}\" deleted.");
    }
}
