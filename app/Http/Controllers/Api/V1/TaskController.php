<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskList;
use App\Services\BadgeService;
use App\Services\PointsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private PointsService $pointsService,
        private BadgeService $badgeService,
    ) {}

    /**
     * Get all tasks for the current family with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();
        $user = $request->user();

        $query = Task::whereHas('taskList', function ($q) use ($family) {
            $q->where('family_id', $family->id);
        });

        // Filter by assigned_to
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->query('assigned_to'));
        }

        // Filter by task list
        if ($request->filled('list')) {
            $query->where('task_list_id', $request->query('list'));
        }

        // Filter by status (complete/incomplete)
        if ($request->filled('status')) {
            if ($request->query('status') === 'complete') {
                $query->whereNotNull('completed_at');
            } else {
                $query->whereNull('completed_at');
            }
        }

        // Filter by due date range
        if ($request->filled('due_before')) {
            $query->where('due_date', '<=', Carbon::parse($request->query('due_before')));
        }
        if ($request->filled('due_after')) {
            $query->where('due_date', '>=', Carbon::parse($request->query('due_after')));
        }

        // Filter by is_family_task
        if ($request->filled('is_family_task')) {
            $query->where('is_family_task', $request->boolean('is_family_task'));
        }

        $tasks = $query
            ->with(['creator', 'assignee', 'taskList'])
            ->orderBy('due_date')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'tasks' => TaskResource::collection($tasks),
        ], 200);
    }

    /**
     * Get tasks for a specific task list.
     */
    public function indexForList(Request $request, TaskList $taskList): JsonResponse
    {
        $this->authorize('view', $taskList);

        $tasks = $taskList->tasks()
            ->with(['creator', 'assignee'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'tasks' => TaskResource::collection($tasks),
        ], 200);
    }

    /**
     * Create a new task.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();
        $validated = $request->validated();

        // Ensure task list belongs to current family
        $taskList = TaskList::where('family_id', $family->id)
            ->findOrFail($validated['task_list_id']);

        // Validate assigned_to user is in same family if provided
        if ($request->filled('assigned_to')) {
            $assignedUser = $family->members()->findOrFail($validated['assigned_to']);
        }

        $task = Task::create([
            'family_id' => $family->id,
            'task_list_id' => $validated['task_list_id'],
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'is_family_task' => $validated['is_family_task'] ?? false,
            'points' => $validated['points'] ?? null,
            'recurrence_rule' => $validated['recurrence_rule'] ?? null,
            'recurrence_end' => $validated['recurrence_end'] ?? null,
            'sort_order' => Task::where('task_list_id', $validated['task_list_id'])->max('sort_order') + 1,
        ]);

        return response()->json([
            'task' => TaskResource::make($task->load(['creator', 'assignee'])),
        ], 201);
    }

    /**
     * Get a specific task.
     */
    public function show(Request $request, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $task->load(['creator', 'assignee']);

        return response()->json([
            'task' => TaskResource::make($task),
        ], 200);
    }

    /**
     * Update a task.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        // Validate assigned_to user is in same family if being changed
        if ($request->filled('assigned_to') && $validated['assigned_to'] !== $task->assigned_to) {
            $family = $task->taskList->family;
            $family->members()->findOrFail($validated['assigned_to']);
        }

        $task->update($validated);

        return response()->json([
            'task' => TaskResource::make($task->load(['creator', 'assignee'])),
        ], 200);
    }

    /**
     * Delete a task.
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(null, 204);
    }

    /**
     * Mark a task as complete.
     */
    public function complete(Request $request, Task $task): JsonResponse
    {
        $this->authorize('complete', $task);

        $user = $request->user();

        $task->update([
            'completed_at' => now(),
        ]);

        // Award points
        $transaction = $this->pointsService->awardTaskPoints($task, $user);

        // Check for newly earned badges
        $newBadges = $this->badgeService->checkAndAwardBadges($user);

        $response = [
            'task' => TaskResource::make($task->load(['creator', 'assignee'])),
            'points_earned' => $transaction->points,
        ];

        if (!empty($newBadges)) {
            $response['badges_earned'] = collect($newBadges)->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'description' => $b->description,
                'icon' => $b->icon,
                'color' => $b->color,
            ]);
        }

        return response()->json($response, 200);
    }

    /**
     * Mark a task as incomplete.
     */
    public function uncomplete(Request $request, Task $task): JsonResponse
    {
        $this->authorize('complete', $task);

        $user = $request->user();

        $task->update([
            'completed_at' => null,
        ]);

        // Reverse points
        $this->pointsService->reverseTaskPoints($task, $user);

        return response()->json([
            'task' => TaskResource::make($task->load(['creator', 'assignee'])),
        ], 200);
    }
}
