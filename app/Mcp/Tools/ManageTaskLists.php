<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\TaskList;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('manage-task-lists')]
#[Description('List or create task lists. Actions: list (all task lists with task counts), create (new task list).')]
class ManageTaskLists extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'action' => [
                    'type' => 'string',
                    'enum' => ['list', 'create'],
                    'description' => 'Action to perform',
                ],
                'name' => [
                    'type' => 'string',
                    'description' => 'Name for the new task list (required for create)',
                ],
            ],
            'required' => ['action'],
        ];
    }

    public function handle(Request $request): Response
    {
        $action = $request->get('action');

        return match ($action) {
            'list' => $this->list(),
            'create' => $this->create($request),
            default => Response::error("Unknown action: {$action}"),
        };
    }

    private function list(): Response
    {
        $lists = TaskList::where('family_id', $this->familyId())
            ->withCount(['tasks', 'tasks as incomplete_count' => fn ($q) => $q->whereNull('completed_at')])
            ->orderBy('sort_order')
            ->get();

        return Response::json([
            'task_lists' => $lists->map(fn ($l) => [
                'id' => $l->id,
                'name' => $l->name,
                'total_tasks' => $l->tasks_count,
                'incomplete_tasks' => $l->incomplete_count,
            ])->toArray(),
        ]);
    }

    private function create(Request $request): Response
    {
        $name = $request->get('name');
        if (!$name) {
            return Response::error('name is required to create a task list.');
        }

        $list = TaskList::create([
            'family_id' => $this->familyId(),
            'name' => $name,
            'sort_order' => TaskList::where('family_id', $this->familyId())->max('sort_order') + 1,
        ]);

        return Response::json([
            'message' => "Task list \"{$list->name}\" created.",
            'task_list' => ['id' => $list->id, 'name' => $list->name],
        ]);
    }
}
