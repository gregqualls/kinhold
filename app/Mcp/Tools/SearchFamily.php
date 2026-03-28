<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Task;
use App\Models\VaultEntry;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('search-family')]
#[Description('Search across all family data — tasks, vault entries, and family members. Returns matching results from all modules.')]
#[IsReadOnly]
class SearchFamily extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'Search keywords',
                ],
            ],
            'required' => ['query'],
        ];
    }

    public function handle(Request $request): Response
    {
        $query = $request->get('query');
        $familyId = $this->familyId();
        $user = $this->user();
        $results = [];

        // Search tasks
        $tasks = Task::where('family_id', $familyId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('description', 'ilike', "%{$query}%");
            })
            ->limit(10)
            ->get();

        if ($tasks->isNotEmpty()) {
            $results['tasks'] = $tasks->map(fn ($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'status' => $t->isComplete() ? 'completed' : 'pending',
                'due_date' => $t->due_date?->format('Y-m-d'),
                'assigned_to' => $t->assignee?->name,
            ])->toArray();
        }

        // Search vault entries (respecting permissions)
        $vaultQuery = VaultEntry::where('family_id', $familyId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('notes', 'ilike', "%{$query}%");
            });

        if (!$user->isParent()) {
            $vaultQuery->whereHas('permissions', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $vaultEntries = $vaultQuery->limit(10)->get();

        if ($vaultEntries->isNotEmpty()) {
            $results['vault_entries'] = $vaultEntries->map(fn ($v) => [
                'id' => $v->id,
                'title' => $v->title,
                'category' => $v->category?->name,
            ])->toArray();
        }

        // Search family members
        $members = $this->family()->members()
            ->where(function ($q) use ($query) {
                $q->where('name', 'ilike', "%{$query}%")
                    ->orWhere('email', 'ilike', "%{$query}%");
            })
            ->limit(5)
            ->get();

        if ($members->isNotEmpty()) {
            $results['members'] = $members->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'role' => $m->family_role->value ?? $m->family_role,
            ])->toArray();
        }

        if (empty($results)) {
            return Response::text("No results found for \"{$query}\".");
        }

        return Response::json($results);
    }
}
