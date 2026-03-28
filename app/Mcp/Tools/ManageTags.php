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
#[Description('List, create, or delete task tags. Actions: list, create, delete.')]
class ManageTags extends Tool
{
    use ScopesToFamily;

    public function schema($schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'action' => [
                    'type' => 'string',
                    'enum' => ['list', 'create', 'delete'],
                    'description' => 'Action to perform',
                ],
                'tag_id' => [
                    'type' => 'string',
                    'description' => 'Tag UUID (required for delete)',
                ],
                'name' => [
                    'type' => 'string',
                    'description' => 'Tag name (required for create)',
                ],
                'color' => [
                    'type' => 'string',
                    'description' => 'Hex color for the tag (e.g. #FF5733)',
                ],
            ],
            'required' => ['action'],
        ];
    }

    public function handle(Request $request): Response
    {
        return match ($request->get('action')) {
            'list' => $this->listTags(),
            'create' => $this->createTag($request),
            'delete' => $this->deleteTag($request),
            default => Response::error("Unknown action: {$request->get('action')}"),
        };
    }

    private function listTags(): Response
    {
        $tags = Tag::where('family_id', $this->familyId())
            ->withCount('tasks')
            ->orderBy('sort_order')
            ->get();

        return Response::json([
            'tags' => $tags->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'color' => $t->color,
                'task_count' => $t->tasks_count,
            ])->toArray(),
        ]);
    }

    private function createTag(Request $request): Response
    {
        $name = $request->get('name');
        if (!$name) {
            return Response::error('name is required to create a tag.');
        }

        $tag = Tag::create([
            'family_id' => $this->familyId(),
            'name' => $name,
            'color' => $request->get('color'),
            'sort_order' => Tag::where('family_id', $this->familyId())->max('sort_order') + 1,
        ]);

        return Response::json([
            'message' => "Tag \"{$tag->name}\" created.",
            'tag' => ['id' => $tag->id, 'name' => $tag->name, 'color' => $tag->color],
        ]);
    }

    private function deleteTag(Request $request): Response
    {
        $tagId = $request->get('tag_id');
        if (!$tagId) {
            return Response::error('tag_id is required for delete.');
        }

        $tag = Tag::where('family_id', $this->familyId())->findOrFail($tagId);
        $name = $tag->name;
        $tag->delete();

        return Response::text("Tag \"{$name}\" deleted.");
    }
}
