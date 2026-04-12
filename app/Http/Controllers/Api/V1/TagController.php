<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        $tags = Tag::where('family_id', $family->id)
            ->withCount('tasks')
            ->withCount(['tasks as incomplete_tasks_count' => function ($query) {
                $query->whereNull('completed_at');
            }])
            ->withCount('recipes')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'tags' => TagResource::collection($tags),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Tag::class);

        $family = $request->user()->currentFamily()->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        $tag = Tag::firstOrCreate(
            [
                'family_id' => $family->id,
                'name' => $validated['name'],
            ],
            [
                'color' => $validated['color'] ?? null,
                'sort_order' => Tag::where('family_id', $family->id)->max('sort_order') + 1,
            ]
        );

        return response()->json([
            'tag' => TagResource::make($tag),
        ], $tag->wasRecentlyCreated ? 201 : 200);
    }

    public function update(Request $request, Tag $tag): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();
        abort_unless($tag->family_id === $family->id, 403);
        $this->authorize('update', $tag);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'sometimes|integer',
        ]);

        $tag->update($validated);

        return response()->json([
            'tag' => TagResource::make($tag),
        ]);
    }

    public function destroy(Request $request, Tag $tag): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();
        abort_unless($tag->family_id === $family->id, 403);
        $this->authorize('delete', $tag);

        $tag->delete();

        return response()->json(null, 204);
    }
}
