<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskListRequest;
use App\Http\Resources\TaskListResource;
use App\Models\TaskList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskListController extends Controller
{
    /**
     * List all task lists for the current family.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        $taskLists = TaskList::where('family_id', $family->id)
            ->withCount('tasks')
            ->withCount(['tasks as incomplete_tasks_count' => function ($query) {
                $query->whereNull('completed_at');
            }])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'task_lists' => TaskListResource::collection($taskLists),
        ], 200);
    }

    /**
     * Create a new task list.
     *
     * @param StoreTaskListRequest $request
     * @return JsonResponse
     */
    public function store(StoreTaskListRequest $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();
        $validated = $request->validated();

        $taskList = TaskList::create([
            'family_id' => $family->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'color' => $validated['color'] ?? null,
            'sort_order' => TaskList::where('family_id', $family->id)->max('sort_order') + 1,
        ]);

        return response()->json([
            'task_list' => TaskListResource::make($taskList->load('tasks')),
        ], 201);
    }

    /**
     * Get a specific task list with its tasks.
     *
     * @param Request $request
     * @param TaskList $taskList
     * @return JsonResponse
     */
    public function show(Request $request, TaskList $taskList): JsonResponse
    {
        $this->authorize('view', $taskList);

        $taskList->load(['tasks' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        return response()->json([
            'task_list' => TaskListResource::make($taskList),
        ], 200);
    }

    /**
     * Update a task list.
     *
     * @param Request $request
     * @param TaskList $taskList
     * @return JsonResponse
     */
    public function update(Request $request, TaskList $taskList): JsonResponse
    {
        $this->authorize('update', $taskList);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
        ]);

        $taskList->update($validated);

        return response()->json([
            'task_list' => TaskListResource::make($taskList),
        ], 200);
    }

    /**
     * Delete a task list.
     *
     * @param Request $request
     * @param TaskList $taskList
     * @return JsonResponse
     */
    public function destroy(Request $request, TaskList $taskList): JsonResponse
    {
        $this->authorize('delete', $taskList);

        $taskList->delete();

        return response()->json(null, 204);
    }
}
