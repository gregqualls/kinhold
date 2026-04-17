<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TagScope;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        $query = Tag::where('family_id', $family->id)
            ->withCount('tasks')
            ->withCount(['tasks as incomplete_tasks_count' => function ($query) {
                $query->whereNull('completed_at');
            }])
            ->withCount('recipes')
            ->withCount('restaurants')
            ->orderBy('sort_order');

        $scope = $request->query('scope');
        if ($scope && in_array($scope, ['task', 'food'], true)) {
            $query->where('scope', $scope);
        }

        return response()->json([
            'tags' => TagResource::collection($query->get()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Tag::class);

        $family = $request->user()->currentFamily()->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
            'scope' => ['nullable', Rule::in(['task', 'food'])],
        ]);

        $scope = $validated['scope'] ?? TagScope::Task->value;

        $tag = Tag::firstOrCreate(
            [
                'family_id' => $family->id,
                'name' => $validated['name'],
                'scope' => $scope,
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
            'scope' => ['sometimes', Rule::in(['task', 'food'])],
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
