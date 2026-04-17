<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\ScopesToFamily;
use App\Models\Tag;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('manage-tags')]
#[Description('List, create, or delete tags. Tags are scoped to either tasks or food (recipes + restaurants). Actions: list, create, delete.')]
class ManageTags extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'action' => $schema->string()->required()->enum(['list', 'create', 'delete'])->description('Action to perform'),
            'tag_id' => $schema->string()->description('Tag UUID (required for delete)'),
            'name' => $schema->string()->description('Tag name (required for create)'),
            'color' => $schema->string()->description('Hex color for the tag (e.g. #FF5733)'),
            'scope' => $schema->string()->enum(['task', 'food'])->description('Tag scope. "task" tags appear on tasks; "food" tags appear on recipes/restaurants. Defaults to "task" on create. Acts as a filter on list.'),
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'list' => $this->listTags($request),
            'create' => $this->createTag($request),
            'delete' => $this->deleteTag($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function listTags(Request $request): Response
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
                'scope' => $t->scope?->value ?? 'task',
                'task_count' => $t->tasks_count,
                'recipe_count' => $t->recipes_count,
                'restaurant_count' => $t->restaurants_count,
            ])->toArray(),
        ]);
    }

    private function createTag(Request $request): Response
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

    private function deleteTag(Request $request): Response
    {
        $tagId = $request->get('tag_id');
        if (! $tagId) {
            return Response::error('tag_id is required for delete.');
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
