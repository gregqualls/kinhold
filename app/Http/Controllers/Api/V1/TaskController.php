<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskCompletedNotification;
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

        $query = Task::where('family_id', $family->id);

        // Filter by assigned_to
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->query('assigned_to'));
        }

        // Filter by tags (comma-separated tag IDs)
        if ($request->filled('tags')) {
            $tagIds = explode(',', $request->query('tags'));
            $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            });
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
            ->with(['creator', 'assignee', 'tags', 'parentTask'])
            ->orderByRaw('completed_at IS NOT NULL')
            ->orderBy('due_date')
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

        // Validate assigned_to user is in same family if provided
        if ($request->filled('assigned_to')) {
            $family->members()->findOrFail($validated['assigned_to']);
        }

        // Enforce task assignment permissions — if user can't assign to others,
        // silently force assigned_to to be their own ID or null
        $assignedTo = $validated['assigned_to'] ?? null;
        if ($assignedTo && $assignedTo !== $request->user()->id && !$family->userCanAssignTasks($request->user())) {
            $assignedTo = $request->user()->id;
        }

        // Children cannot set custom points on tasks — only parents can
        $points = $validated['points'] ?? null;
        if (!$request->user()->isParent()) {
            $points = null;
        }

        $task = Task::create([
            'family_id' => $family->id,
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $assignedTo,
            'due_date' => $validated['due_date'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'is_family_task' => $validated['is_family_task'] ?? false,
            'points' => $points,
            'recurrence_rule' => $validated['recurrence_rule'] ?? null,
            'recurrence_end' => $validated['recurrence_end'] ?? null,
            'sort_order' => Task::where('family_id', $family->id)->max('sort_order') + 1,
        ]);

        // Sync tags
        if (isset($validated['tag_ids'])) {
            $task->tags()->sync($validated['tag_ids']);
        }

        // Notify assignee if task is assigned to someone other than the creator
        if ($task->assigned_to && $task->assigned_to !== $request->user()->id) {
            $assignee = User::find($task->assigned_to);
            if ($assignee) {
                $assignee->notify(new TaskAssignedNotification($task, $request->user()));
            }
        }

        return response()->json([
            'task' => TaskResource::make($task->load(['creator', 'assignee', 'tags', 'family'])),
        ], 201);
    }

    /**
     * Get a specific task.
     */
    public function show(Request $request, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $task->load(['creator', 'assignee', 'tags']);

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

        // Track if assignee is changing
        $previousAssignee = $task->assigned_to;

        // Validate assigned_to user is in same family if being changed
        if ($request->filled('assigned_to') && ($validated['assigned_to'] ?? null) !== $task->assigned_to) {
            $family = $request->user()->currentFamily()->firstOrFail();
            $family->members()->findOrFail($validated['assigned_to']);
        }

        // Enforce task assignment permissions — if user can't assign to others,
        // silently force assigned_to to be their own ID or null
        if (array_key_exists('assigned_to', $validated)) {
            $newAssignedTo = $validated['assigned_to'] ?? null;
            if ($newAssignedTo && $newAssignedTo !== $request->user()->id) {
                $family = $family ?? $request->user()->currentFamily()->firstOrFail();
                if (!$family->userCanAssignTasks($request->user())) {
                    $validated['assigned_to'] = $request->user()->id;
                }
            }
        }

        // Children cannot set custom points on tasks — only parents can
        if (!$request->user()->isParent() && array_key_exists('points', $validated)) {
            unset($validated['points']);
        }

        // Extract tag_ids before update
        $tagIds = $validated['tag_ids'] ?? null;
        unset($validated['tag_ids']);

        $task->update($validated);

        // Sync tags if provided
        if ($tagIds !== null) {
            $task->tags()->sync($tagIds);
        }

        // Notify new assignee if assignment changed
        $newAssignee = $task->assigned_to;
        if ($newAssignee && $newAssignee !== $previousAssignee && $newAssignee !== $request->user()->id) {
            $assignee = User::find($newAssignee);
            if ($assignee) {
                $assignee->notify(new TaskAssignedNotification($task, $request->user()));
            }
        }

        return response()->json([
            'task' => TaskResource::make($task->load(['creator', 'assignee', 'tags'])),
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

        // Children cannot earn points from tasks they created themselves
        // (prevents gaming: create task → set points → complete for free points)
        $skipPoints = !$user->isParent()
            && $task->created_by === $user->id;

        // Award points (0 if self-created by a child)
        $transaction = $skipPoints
            ? $this->pointsService->awardTaskPoints($task, $user, forceZero: true)
            : $this->pointsService->awardTaskPoints($task, $user);

        // Check for newly earned badges
        $newBadges = $this->badgeService->checkAndAwardBadges($user);

        // Notify family members about task completion
        $family = $user->currentFamily()->firstOrFail();
        $family->members()
            ->where('id', '!=', $user->id)
            ->get()
            ->each(function (User $member) use ($task, $user) {
                $member->notify(new TaskCompletedNotification($task, $user));
            });

        $response = [
            'task' => TaskResource::make($task->load(['creator', 'assignee', 'tags'])),
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
            'task' => TaskResource::make($task->load(['creator', 'assignee', 'tags'])),
        ], 200);
    }
}
