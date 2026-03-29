<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardPurchase;
use App\Services\BadgeService;
use App\Services\PointsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RewardsController extends Controller
{
    public function __construct(
        private PointsService $pointsService,
        private BadgeService $badgeService,
    ) {}

    /**
     * List family rewards.
     */
    public function index(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        $rewards = Reward::where('family_id', $family->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('point_cost')
            ->get();

        return response()->json([
            'rewards' => $rewards,
        ]);
    }

    /**
     * Create a reward (parent only).
     */
    public function store(Request $request): JsonResponse
    {
        if (!$request->user()->isParent()) {
            return response()->json(['message' => 'Only parents can create rewards'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'point_cost' => 'required|integer|min:1',
            'icon' => 'nullable|string|max:50',
        ]);

        $family = $request->user()->currentFamily()->firstOrFail();

        $reward = Reward::create([
            'family_id' => $family->id,
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'point_cost' => $validated['point_cost'],
            'icon' => $validated['icon'] ?? null,
        ]);

        return response()->json([
            'reward' => $reward,
        ], 201);
    }

    /**
     * Update a reward (parent only).
     */
    public function update(Request $request, Reward $reward): JsonResponse
    {
        abort_unless($reward->family_id === $request->user()->family_id, 404);

        if (!$request->user()->isParent()) {
            return response()->json(['message' => 'Only parents can update rewards'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'point_cost' => 'sometimes|integer|min:1',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer',
        ]);

        $reward->update($validated);

        return response()->json([
            'reward' => $reward,
        ]);
    }

    /**
     * Delete a reward (parent only).
     */
    public function destroy(Request $request, Reward $reward): JsonResponse
    {
        abort_unless($reward->family_id === $request->user()->family_id, 404);

        if (!$request->user()->isParent()) {
            return response()->json(['message' => 'Only parents can delete rewards'], 403);
        }

        $reward->delete();

        return response()->json(null, 204);
    }

    /**
     * Purchase a reward.
     */
    public function purchase(Request $request, Reward $reward): JsonResponse
    {
        $user = $request->user();
        abort_unless($reward->family_id === $user->family_id, 404);

        try {
            $result = $this->pointsService->redeemReward($reward, $user);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        // Check for badges
        $newBadges = $this->badgeService->checkAndAwardBadges($user);

        $response = [
            'message' => "Purchased: {$reward->title}",
            'purchase' => $result['purchase'],
            'new_bank' => $user->pointBank(),
        ];

        if (!empty($newBadges)) {
            $response['badges_earned'] = collect($newBadges)->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'icon' => $b->icon,
                'color' => $b->color,
            ]);
        }

        return response()->json($response, 201);
    }

    /**
     * List purchases.
     */
    public function purchases(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        $purchases = RewardPurchase::where('family_id', $family->id)
            ->with(['reward:id,title,icon,point_cost', 'user:id,name,avatar'])
            ->orderByDesc('purchased_at')
            ->paginate(30);

        return response()->json([
            'purchases' => $purchases->items(),
            'pagination' => [
                'current_page' => $purchases->currentPage(),
                'last_page' => $purchases->lastPage(),
                'total' => $purchases->total(),
            ],
        ]);
    }
}
