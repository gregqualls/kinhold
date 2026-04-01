<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PointTransaction;
use App\Services\BadgeService;
use App\Services\PointsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function __construct(
        private PointsService $pointsService,
        private BadgeService $badgeService,
    ) {}

    /**
     * Get current user's point bank balance.
     */
    public function bank(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'bank' => $this->pointsService->getBank($user),
        ]);
    }

    /**
     * Get family leaderboard.
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        return response()->json([
            'leaderboard' => $this->pointsService->getLeaderboard($family),
            'period' => $family->getLeaderboardPeriod(),
        ]);
    }

    /**
     * Get family-wide activity feed.
     */
    public function feed(Request $request): JsonResponse
    {
        $family = $request->user()->currentFamily()->firstOrFail();

        $transactions = PointTransaction::where('family_id', $family->id)
            ->with(['user:id,name,avatar', 'awardedBy:id,name,avatar'])
            ->orderByDesc('created_at')
            ->paginate(30);

        return response()->json([
            'feed' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    /**
     * Give kudos to a family member.
     */
    public function kudos(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:255',
        ]);

        $from = $request->user();

        // Block self-kudos
        if ($validated['user_id'] === $from->id) {
            return response()->json(['message' => "You can't give kudos to yourself"], 422);
        }

        $family = $from->currentFamily()->firstOrFail();
        $to = $family->members()->findOrFail($validated['user_id']);

        try {
            $transaction = $this->pointsService->giveKudos($from, $to, $family, $validated['reason']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        // Check for badges on the recipient
        $newBadges = $this->badgeService->checkAndAwardBadges($to);

        $response = [
            'transaction' => $transaction,
            'message' => "Gave kudos to {$to->name}",
        ];

        if (! empty($newBadges)) {
            $response['badges_earned'] = collect($newBadges)->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'description' => $b->description,
                'icon' => $b->icon,
                'color' => $b->color,
                'user_id' => $to->id,
            ]);
        }

        return response()->json($response, 201);
    }

    /**
     * Deduct points from a family member (parent only).
     */
    public function deduct(Request $request): JsonResponse
    {
        $from = $request->user();

        if (! $from->isParent()) {
            return response()->json(['message' => 'Only parents can deduct points'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $family = $from->currentFamily()->firstOrFail();
        $target = $family->members()->findOrFail($validated['user_id']);

        $transaction = $this->pointsService->deductPoints(
            $from,
            $target,
            $validated['points'],
            $validated['reason']
        );

        return response()->json([
            'transaction' => $transaction,
            'message' => "Deducted {$validated['points']} points from {$target->name}",
        ], 201);
    }
}
