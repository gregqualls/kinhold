<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Carbon\Carbon;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('manage-tasks')]
#[Description('List, create, update, or delete tasks. Actions: list (with filters), create, update, delete.')]
class ManageTasks extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'action' => [
                    'type' => 'string',
                    'enum' => ['list', 'create', 'update', 'delete'],
                    'description' => 'Action to perform',
                ],
                'task_id' => [
                    'type' => 'string',
                    'description' => 'Task UUID (required for update/delete)',
                ],
                'list_id' => [
                    'type' => 'string',
                    'description' => 'Task list UUID to filter by or assign to',
                ],
                'title' => [
                    'type' => 'string',
                    'description' => 'Task title (required for create)',
                ],
                'description' => [
                    'type' => 'string',
                    'description' => 'Task description',
                ],
                'assigned_to' => [
                    'type' => 'string',
                    'description' => 'UUID of the family member to assign the task to',
                ],
                'due_date' => [
                    'type' => 'string',
                    'description' => 'Due date in YYYY-MM-DD format',
                ],
                'priority' => [
                    'type' => 'string',
                    'enum' => ['low', 'medium', 'high'],
                    'description' => 'Task priority',
                ],
                'is_family_task' => [
                    'type' => 'boolean',
                    'description' => 'Whether any family member can complete this task',
                ],
                'points' => [
                    'type' => 'integer',
                    'description' => 'Points awarded on completion (parent only)',
                ],
                'status' => [
                    'type' => 'string',
                    'enum' => ['pending', 'completed'],
                    'description' => 'Filter by status (for list action)',
                ],
                'tag_ids' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Array of tag UUIDs to attach',
                ],
            ],
            'required' => ['action'],
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'list' => $this->listTasks($request),
            'create' => $this->createTask($request),
            'update' => $this->updateTask($request),
            'delete' => $this->deleteTask($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function listTasks(Request $request): Response
    {
        $query = Task::where('family_id', $this->familyId());

        if ($listId = $request->get('list_id')) {
            $query->where('task_list_id', $listId);
        }
        if ($assignedTo = $request->get('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }
        if ($status = $request->get('status')) {
            $status === 'completed'
                ? $query->whereNotNull('completed_at')
                : $query->whereNull('completed_at');
        }

        $tasks = $query->with(['assignee', 'taskList', 'tags'])
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
                'list' => $t->taskList?->name,
                'is_family_task' => $t->is_family_task,
                'points' => $t->getEffectivePoints(),
                'tags' => $t->tags->pluck('name')->toArray(),
                'is_recurring' => $t->isRecurring(),
            ])->toArray(),
        ]);
    }

    private function createTask(Request $request): Response
    {
        $title = $request->get('title');
        if (!$title) {
            return Response::error('title is required to create a task.');
        }

        $family = $this->family();
        $user = $this->user();

        // Validate list belongs to family
        if ($listId = $request->get('list_id')) {
            TaskList::where('family_id', $family->id)->findOrFail($listId);
        }

        // Validate assignee belongs to family
        $assignedTo = $request->get('assigned_to');
        if ($assignedTo) {
            $family->members()->findOrFail($assignedTo);
            if ($assignedTo !== $user->id && !$family->userCanAssignTasks($user)) {
                $assignedTo = $user->id;
            }
        }

        // Children cannot set custom points
        $points = $request->get('points');
        if (!$user->isParent()) {
            $points = null;
        }

        $task = Task::create([
            'family_id' => $family->id,
            'task_list_id' => $listId,
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

        // Sync tags
        if ($tagIds = $request->get('tag_ids')) {
            $task->tags()->sync($tagIds);
        }

        // Notify assignee
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

    private function updateTask(Request $request): Response
    {
        $taskId = $request->get('task_id');
        if (!$taskId) {
            return Response::error('task_id is required for update.');
        }

        $task = Task::where('family_id', $this->familyId())->findOrFail($taskId);
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

        if ($request->get('list_id') !== null) {
            TaskList::where('family_id', $this->familyId())->findOrFail($request->get('list_id'));
            $updates['task_list_id'] = $request->get('list_id');
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

    private function deleteTask(Request $request): Response
    {
        $taskId = $request->get('task_id');
        if (!$taskId) {
            return Response::error('task_id is required for delete.');
        }

        $task = Task::where('family_id', $this->familyId())->findOrFail($taskId);
        $title = $task->title;
        $task->delete();

        return Response::text("Task \"{$title}\" deleted.");
    }
}
